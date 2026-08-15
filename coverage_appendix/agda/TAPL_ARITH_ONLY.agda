{- 
  CH 3:  Untyped arithmetic expressions
  Author: Brigitte Pientka

  EVALUATION RULES AND META-THEORY
  (follows TAPL CH3, part for numbers only)

-}
module TAPL_ARITH_ONLY where

data Tm : Set where
  z       : Tm
  succ    : Tm -> Tm
  pred    : Tm -> Tm
  
data Value : Tm -> Set where
  v_z     : Value z
  v_s     : {n : Tm} -> Value n -> Value (succ n)

data Step : Tm -> Tm -> Set where 
  s_succ         : {n : Tm} -> {m : Tm} -> 
                   Step n m -> Step (succ n) (succ m)
  s_pred_zero    : Step (pred z) z
  s_pred_succ    : {m : Tm} -> Value m -> Step (pred (succ m)) m
  s_pred         : {n : Tm} -> {m : Tm} -> 
                   Step n m -> Step (pred n) (pred m)

data Eq : Tm -> Tm -> Set where
  ref : {m : Tm} -> Eq  m m

-- Proof that small-step semantics is deterministic 

data Empty : Set where

values_dont_step : {m : Tm} -> {m' : Tm} -> Step m m' -> Value m  -> Empty  
values_dont_step ()          (v_z)
values_dont_step (s_succ t)  (v_s nv)    = values_dont_step t  nv

det : {m : Tm} -> {n1 : Tm} -> {n2 : Tm} ->
      Step m n1 -> Step m n2 -> Eq n1 n2 
det (s_succ t)         (s_succ t')          with det t t'
... | ref = ref
det (s_pred_zero)      (s_pred_zero)      = ref
det (s_pred_succ _)    (s_pred_succ _)    = ref
det (s_pred t)         (s_pred t')          with det t t'
... | ref = ref
-- Impossible cases for pred
det (s_pred t)         (s_pred_succ nv)     with values_dont_step t (v_s nv)
... | ()
det (s_pred_succ nv)   (s_pred t)           with values_dont_step t (v_s nv)
... | ()
det (s_pred ())        (s_pred_zero)
det (s_pred_zero)      (s_pred ())      

