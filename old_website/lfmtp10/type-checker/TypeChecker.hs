module TypeChecker where

import Control.Monad.Error  
import Control.Monad.Reader 

-- sorts s
data Sort = Kind | Type 
            deriving (Eq, Ord, Show)

-- type of de Bruijn indices >= 1
type DBI = Int

-- natural numbers >= 0
type Nat = Int

-- expressions e
data Exp  = Sort Sort | Const Const | Pi Exp Exp 
          | Var DBI | MVar DBI | Abs Exp | App Exp Exp
          | ESub Sub Exp | MSub MSub Exp
          | Clos Env MEnv Exp -- optimization of whnf
            deriving (Eq, Ord, Show)

-- kinds k
type Kind = Exp

-- types a,b
type Type = Exp

-- terms t
type Term = Exp

-- substitutions sigma, tau
data Sub  = Shift Nat | Ext Sub Term | Comp Sub Sub | MComp MSub Sub 
            deriving (Eq, Ord, Show)

-- meta substitutions theta
type MSub = Sub  -- no MComp

-- identity (meta) substitution
sid = Shift 0

-- weak head normalization

type Whnf = Exp
type WNe  = Exp
type Clos = Exp

{- Whnf w 
data Whnf = Sort Sort 
          | Clos Env MEnv (Pi Exp Exp) -- ESub Env (MSub MEnv (Pi Exp Exp)) 
          | Clos Env MEnv (Abs Exp)    -- ESub Env (MSub MEnv (Abs Exp))
          | WNe
data WNe  = Const Const | Var DBI | ESub Sub (MVar DBI) | App WNe Clos
-}

clos :: Env -> MEnv -> Exp -> Clos
clos = Clos
-- clos rho eta t = ESub rho (MSub eta t)

shiftClos :: Nat -> Clos -> Clos
shiftClos n (Var m)          = Var (n + m)
shiftClos n (Clos rho eta t) = Clos (shiftEnv n rho) eta t 

shiftNe :: Nat -> WNe -> WNe
shiftNe n (Const c)             = Const c
shiftNe n (Var m)               = Var (n + m)
shiftNe n (ESub sigma (MVar m)) = ESub (Comp (Shift n) sigma) (MVar m)
shiftNe n (App u cl)            = App (shiftNe n u) (shiftClos n cl)

liftNe :: WNe -> WNe
liftNe u = App (shiftNe 1 u) (Var 1)

type Env  = Sub
type MEnv = MSub

{- Env rho
data Env  = Shift n 
          | Ext rho t 
          | Comp (Shift n) rho  -- to account for liftEnving 
-- MEnv eta
data MEnv = Shift n 
          | Ext eta t
-}

-- shifting an environment lazily
shiftEnv :: Nat -> Env -> Env
shiftEnv 0 rho = rho
shiftEnv n rho = Comp (Shift n) rho

-- lifting an environment unter a binder
liftEnv :: Env -> Env
liftEnv rho = Ext (Comp (Shift 1) rho) (Var 1)

whnf' :: Exp -> Whnf
whnf' (Clos rho eta t) = whnf rho eta t
whnf' t = whnf sid sid t

whnf :: Env -> MEnv -> Exp -> Whnf
whnf rho eta e = 
  case e of
     Sort s      -> Sort s
     Const c     -> Const c
     Pi a b      -> clos rho eta (Pi a b)
     Var x       -> wlookup 0 rho x
     MVar x      -> wmlookup rho eta x
     Abs t       -> clos rho eta (Abs t)
     App t u     -> whnf rho eta t `wapp` clos rho eta u
     ESub tau t  -> whnf (wsub rho eta tau) eta t
     MSub theta t -> whnf rho (wmsub eta theta) t
     Clos rho' eta' t -> whnf (wsub rho eta rho') (wmsub eta eta') t

wapp :: Whnf -> Clos -> Whnf
-- wapp (ESub rho (MSub eta (Abs t))) cl = whnf (Ext rho cl) eta t
wapp (Clos rho eta (Abs t)) cl = whnf (Ext rho cl) eta t
wapp w                      cl = App w cl     

wlookup :: Nat -> Env -> DBI -> Whnf
wlookup k (Shift n)                m = Var (k + n + m)
-- wlookup k (Ext rho t)          1 = whnf (Shift k) sid t
wlookup k (Ext rho (Var m))        1 = Var (k + m)
wlookup k (Ext _ (Clos rho eta t)) 1 = whnf (shiftEnv k rho) eta t
wlookup k (Ext rho t)          (m+1) = wlookup k rho m
wlookup k (Comp (Shift n) rho)     m = wlookup (k + n) rho m

wmlookup :: Env -> MEnv -> DBI -> Whnf
wmlookup rho (Shift n)       m = Var (n + m)
wmlookup rho (Ext eta t)     1 = whnf rho sid t
wmlookup rho (Ext eta t) (m+1) = wmlookup rho eta m

-- normalize a substitution into an environment
 
wsub  :: Env -> MEnv -> Sub -> Env
wsub = wsub' 0

wsub' :: Nat -> Env -> MEnv -> Sub -> Env
wsub' k (Comp (Shift m) rho) eta sigma = wsub' (k + m) rho eta sigma
wsub' k rho          eta (Shift 0)     = shiftEnv k rho
wsub' k (Shift m)    eta (Shift n)     = Shift (k + m + n)
wsub' k (Ext rho t)  eta (Shift (n+1)) = wsub' k rho eta (Shift n)
wsub' k rho          eta (Ext sigma t) = Ext (wsub' k rho eta sigma) 
                                            (clos (shiftEnv k rho) eta t)
wsub' k rho       eta (Comp sigma tau) = wsub (wsub' k rho eta sigma) eta tau 
wsub' k rho      eta (MComp theta tau) = wsub' k rho (wmsub eta theta) tau

wmsub :: MEnv -> MSub -> MEnv
-- wmsub eta         (Shift 0)     = eta
wmsub (Shift m)   (Shift n)     = Shift (m + n)
wmsub (Ext eta t) (Shift (n+1)) = wmsub eta (Shift n)
wmsub eta (Ext theta t)         = Ext (wmsub eta theta) (clos sid eta t)
wmsub eta (Comp theta theta')   = wmsub (wmsub eta theta) theta' 

-- equality checking

eq    :: Exp -> Exp -> Bool
eq t t' = eqw (whnf' t) (whnf' t')

-- equality of weak head normal forms
eqw   :: Whnf -> Whnf -> Bool

eqw (Sort s) (Sort s') = s == s'

-- Pi-closures 
-- eqw (ESub rho (MSub eta (Pi a b))) (ESub rho' (MSub eta' (Pi a' b'))) =
eqw (Clos rho eta (Pi a b)) (Clos rho' eta' (Pi a' b')) =
  eqw (whnf rho eta a) (whnf rho' eta' a') &&
  eqw (whnf (liftEnv rho) eta b) (whnf (liftEnv rho') eta' b')

-- lambda-closures
-- eqw (ESub rho (MSub eta (Abs t))) (ESub rho' (MSub eta' (Abs t'))) =
eqw (Clos rho eta (Abs t)) (Clos rho' eta' (Abs t')) =
  eqw (whnf (liftEnv rho) eta t) (whnf (liftEnv rho') eta' t')

-- eta
eqw (Clos rho eta (Abs t)) u = eqw (whnf (liftEnv rho) eta t) (liftNe u)
eqw u (Clos rho eta (Abs t)) = eqw (liftNe u) (whnf (liftEnv rho) eta t) 

eqw u u' = eqn u u'

-- equality of neutral weak head normal forms
eqn :: WNe -> WNe -> Bool
eqn (Const c) (Const c') = c == c'
eqn (Var m)   (Var m')   = m == m'
eqn (ESub sigma (MVar m)) (ESub sigma' (MVar m')) = m == m' &&
  eqenv (wsub sid sid sigma) (wsub sid sid sigma')
eqn (App u cl) (App u' cl') = eqn u u' && eq cl cl'
eqn _ _ = False

eqenv :: Env -> Env -> Bool
eqenv = eqenv' 0 0 

eqenv' :: Nat -> Nat -> Env -> Env -> Bool
eqenv' k k' (Comp (Shift n) rho) rho'  = eqenv' (k+n)  k' rho rho'
eqenv' k k' rho (Comp (Shift n') rho') = eqenv' k (k'+n') rho rho'
eqenv' k k' (Shift n)       (Shift n') = k+n == k'+n'
eqenv' k k' (Ext rho t)  (Ext rho' t') = 
  eq (shiftClos k t) (shiftClos k' t') && eqenv' k k' rho rho'
eqenv' k k' _ _ = False

-- contexts as lists
type Cxt = [Type]

-- meta-shift each type in the context
shiftCxt :: Nat -> Cxt -> Cxt
shiftCxt n = map (MSub (Shift n)) 

-- meta contexts
type MEntry = (Cxt, Type)
type MCxt = [MEntry]

-- type checking context
type TCCxt = (MCxt, Cxt)
emptyTCCxt = ([], [])

-- type checking monad
type TCM a = ReaderT TCCxt (Either String) a

-- context extension
extendCxt :: Type -> TCM a -> TCM a
extendCxt a cont = local (\ (delta,gamma) -> (delta, a:gamma)) cont

type Ne   = Exp
type Nf   = Exp
type NSub = Sub

infer  :: Ne -> TCM Clos
check  :: Nf -> Clos -> TCM ()
checks :: NSub -> Cxt -> TCM ()

infer (Const c) = sig c
infer (Var m)   = do
    (delta, gamma) <- ask
    when (m < 1) $ fail $ "illegal de Bruijn index " ++ show m 
    when (m > length gamma) $ fail $ "unbound variable " ++ show m 
    return $ ESub (Shift m) $ gamma !! (m-1)  

infer (ESub rho (MVar m)) = do
    (delta, gamma) <- ask
    when (m < 1) $ fail $ "illegal de Bruijn index " ++ show m 
    when (m > length delta) $ fail $ "unbound meta-variable " ++ show m 
    let (psi, a) = delta !! (m-1)
    checks rho (shiftCxt m psi)
    return $ clos rho (Shift m) a

infer (App u v) = do
    c <- infer u 
    case whnf' c of
      Clos sigma theta (Pi a b) -> do
        check v (clos sigma theta a)
        return (clos (Ext sigma v) theta b)
      _ -> fail $ "expected " ++ show u ++ " to have function type, found "
             ++ show c

infer u = fail $ "cannot infer type of " ++ show u
 
check (Sort Type) c = 
    case whnf' c of
       Sort Kind -> return ()
       _ -> fail $ "expected " ++ show (Sort Type) ++ " to have " 
              ++ show (Sort Kind) ++ ", found " ++ show c

check (Pi v v') c = do
    check v (Sort Type)
    extendCxt v $ check v' c

check (Abs v) c = 
    case whnf' c of 
      Clos sigma theta (Pi a b) ->
        extendCxt (clos sigma theta a) $
          check v (clos (liftEnv sigma) theta b)
      _ -> fail $ "expected " ++ show (Abs v) ++ 
             " to have function type, found " ++ show c 

check u c = do
    a <- infer u
    if eq a c then return () else
      fail $ "inferred type " ++ show a ++ " of expression " ++ show u
        ++ " is not equal to ascribed type " ++ show c

checks (Shift n) [] = do
    (delta, gamma) <- ask
    if length gamma == n then return () else
      fail $ "expected context " ++ show gamma ++ " to have length " ++ show n

checks (Ext rho v) (c : psi) = do
    check v c
    checks rho psi

runCheck :: Nf -> Type -> Either String ()
runCheck t c = runReaderT (check t c) emptyTCCxt 


-- full beta-normalization
norm :: Exp -> Nf
norm t = reify (whnf' t)

reify :: Whnf -> Nf
reify (Sort s) = Sort s
--reify (ESub sigma (MSub theta (Pi a b))) = 
reify (Clos sigma theta (Pi a b)) = 
  Pi (reify (whnf sigma theta a)) (reify (whnf (liftEnv sigma) theta b))
reify (Clos sigma theta (Abs t)) =
  Abs (reify (whnf (liftEnv sigma) theta t))
reify u = reifyNe u

reifyNe :: WNe -> Ne
reifyNe (Const c) = Const c
reifyNe (Var m)   = Var m
reifyNe (ESub sigma (MVar m)) = ESub (reifys (wsub sid sid sigma)) (MVar m)
reifyNe (App u t) = App (reifyNe u) (norm t)

reifys :: Env -> NSub
reifys (Shift n) = Shift n
reifys (Ext sigma t) = Ext (reifys sigma) (norm t)

-- signature
data Const = CNat | CZero | CSucc | CTy | CEl
            deriving (Eq, Ord, Show)

-- signature lookup
sig   :: Const -> TCM Clos
sig CNat  = return $ Sort Type
sig CZero = return $ Const CNat
sig CSucc = return $ Pi (Const CNat) (Const CNat)
sig CTy   = return $ Sort Type
sig CEl   = return $ Const CTy `arr` Sort Type

-- tests

-- non-dependent function space
arr :: Nf -> Nf -> Nf
arr a b = Pi a (norm (ESub (Shift 1) b))

-- Church number 1
a1 = Pi (Sort Type) $ (Var 1 `arr` Var 1) `arr` (Var 1 `arr` Var 1) 
t1 = Abs $ Abs $ Abs $ f `App` x
  where x = Var 1
        f = Var 2

ca1 = runCheck a1 (Sort Type) -- fails, because we do not have polymorphism!
ct1 = runCheck t1 a1          -- ok

-- Church number 1, second try
a2 = Pi (Const CTy) $ (a `arr` a) `arr` (a `arr` a) 
  where a = Const CEl `App` Var 1
t2 = Abs $ Abs $ Abs $ f `App` x
  where x = Var 1
        f = Var 2

ca2 = runCheck a2 (Sort Type) -- ok
ct2 = runCheck t2 a2          -- ok

alltests = all (either (const False) (const True)) [ct1,ca2,ct2]

