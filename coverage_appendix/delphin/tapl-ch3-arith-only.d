(* 
  CH 3:  Untyped arithmetic expressions
  
  AUTHOR: Brigitte Pientka

  SMALL-STEP EVALUATION RULES AND DETERMINACY
*)

(* Load definitions of terms, values, step etc. *)

sig use "tapl-ch3-arith-only.elf";

(* Declare that there are no parameters. By default
   Delphin assumes all type-level constants to be possible
   parameters.
*)

params = . ;

(*
   Congruence lemmas about equality
*)
fun eq_succ: <eq M M'> -> <eq (succ M) (succ M')> = 
    (fn <ref> => <ref>)
;

fun eq_pred: <eq M M'> -> <eq (pred M) (pred M')> = 
    (fn <ref> => <ref>)
;


(* Values don't step 
  
   Theorem: If step M M' and value M then impossible

*)

fun values_dont_step : <step M M'> -> <value M> -> <empty> = 
fn <s_succ S> <v_s V> =>  values_dont_step <S> <V>
;


fun step_zero_impossible : <step z M'> -> <empty> = 

(*  From a contradiction, we can conclude anything. 

   Theorem: For all M M', if empty the neq M' M.

*)

fun empty_implies_anything: <empty> -> <eq M' M''> = 
(fn . )
;

(*
   MAIN THEOREM: If step M M' and step M M'' then eq M' M''.

*)
fun det : <step M M'> -> <step M M''> -> <eq M' M''> =
fn <s_succ DstepM1'>  <s_succ DstepM1''> => 
    eq_succ (det <DstepM1'> <DstepM1''>)
| <s_pred_zero>       <s_pred_zero>      => <ref>
| <s_pred_succ V>     <s_pred_succ _ >   => <ref>
| <s_pred D>          <s_pred F>         => 
   eq_pred (det <D> <F>) 
| <s_pred D>          <s_pred_succ V >   => 
   empty_implies_anything (values_dont_step <D> <v_s V>)
| <s_pred_succ V >    <s_pred D>         => 
   empty_implies_anything (values_dont_step <D> <v_s V>)

;

