<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
   <meta http-equiv="content-type" content="text/html; charset=utf-8" />
   <title>Beluga: Functional Programming with Higher-Order Abstract Syntax</title>
   <link rel="stylesheet" type="text/css" href="../tutorial-style.css" />
</head>

<body>
<script type="text/javascript" language="javascript">
<!--
function rule(name, top, bottom) {
    var top = top.join(" &nbsp; &nbsp; ");
    document.write(
        "<p><table cellpadding=0>" +
        "<tr>" +
            "<td align=center>\n" + top + "<\/td>" +
            "<td><\/td>" +
        "<\/tr>" +
        "<tr style='line-height:0.1'>" +
            "<td align=center'>" +
                "<div style='border-bottom: solid 1px #000'><\/div>" +
            "<\/td>" +
            "<td> " +
                // position:relative is necessary to prevent IE from
                // clipping off the top and bottom of the name
                "<span style='position:relative'><tt>"+name+"<\/tt><\/span>"+
                "<\/td>" +
        "<\/tr>\n" +
        "<tr>" +
            "<td align=center>\n" + bottom + "<\/td>" +
            "<td><\/td>" +
        "<\/tr>" +
        "<\/table><\/p>"
    )
}
// -->
</script>

<?php include "../functions.php"; ?>
<div id="wrapper">
   <?php echoFile("header.html"); ?>
  <div id="menu">
   </div>
<div id="main">

<h1>Beluga Syntax</h1>

<p>We discuss on a high-level the idea behind Beluga's concrete syntax. Beluga is a two-level system: 1) it supports specifications of formal systems in the logical framework LF 2) it supports programming with LF specifications. Hence, Beluga programs consist of two kinds of declarations: LF constant declarations and computation-level declarations.</p>



<pre class="code">
sig ::=                   % Empty signature
     | lf_decl sig        % LF constant declaration
     | c_decl  sig        % Computation-level constant declaration
</pre>

<p>
<strong>Warning:</strong> 
<ul>
<li>
Beluga is a protype and work in progress. As such, some of the subtleties in the syntax may still change over time. While we try to ensure backwards compatibility, this may not always be possible. </li>
<li>This description of the source-level syntax for Beluga below is not exhaustive. It tries to provide a high-level description and intuition for Belgua programs. </li>
</ul>
</p>


<h2>LF declarations</h2>
<p>
LF declarations in Beluga follow closely the syntax used in the <a href="http://twelf.plparty.org/wiki/Main_Page">Twelf system</a>. There are however a few differences which we will mention below.
</p>

<pre class="code">
lf_decl ::= id : term.         % a : K  or  c : A
       | <keyword>%name</keyword> id id.          % name preference declaration

term ::= <keyword>type</keyword>                  % type
       | id                    % variable x or constant a or c
       | term -> term          % A -> B
       | term <- term          % A <- B, same as B -> A
       | {id : term} term      % Pi x:A. K  or  Pi x:A. B
       | \id . term            % lambda x. M 
       | term term             % A M  or  M N
       | _                     % hole, to be filled by term reconstruction
     
</pre>

<p>The constructs <codeinline>{x:U} V</codeinline> and <codeinline>\x. V</codeinline> bind the identifier <codeinline>x</codeinline> in <codeinline>V</codeinline>, which may shadow other constants or bound variables. As usual in type theory, <codeinline>U -> V</codeinline> is treated as an abbreviation for <codeinline>{x:U} V</codeinline> where <codeinline>x</codeinline> does not appear in <codeinline>V</codeinline>. </p>

<p>In the order of precedence, we disambiguate the syntax and impose restrictions as follows:
<ul>
  <li>Juxtaposition (application) is left associative and has highest precedence.</li>
  <li><codeinline>-></codeinline> is right and <codeinline><-</codeinline> left associative with equal precedence.</li>  
  <li><codeinline>:</codeinline> is left associative.</li>  
  <li><codeinline>{}<codeinline> and <codeinline>\ </codeinline> are weak prefix operators. </li>  
  <li>Bound variables and constants must be written with lower-case letters.</li>  
  <li>Free variables can be written with upper-case letters or lower case letters; but we use the convention to write them with upper-case letters.</li>  
  <li>All terms need to be written in beta-normal form, i.e. no redeces can occur in terms. However, variables do not need to be eta-expanded.</li>  
</ul>
</p>

<p> Next, we give a few simple examples illustrating what terms Beluga parses and which ones it does not. For example, the following are parsed identically:

<pre class="code">
   d : a <- b <- {x:term} c x -> p x.
   d : ({x:term} c x -> p x) -> b -> a.
   d : ((a <- b) <- ({x: term} ((c x) -> (p x)))).
</pre>
</p>
<p>
The following term parses: 
<pre class="code">
      d : p x <- a <- b <-  c x.  % against our convention but parses
      d : p X <- a <- b <-  c X.  % Better code: uses convention 

      P:term -> type              % Parse error
</pre>
</p>
<p>
The following will not parse:
<pre class="code">
  d: p ((\x.x) a) .    % this will give an error since there is a redex
  d: p a.              % this will parse.
</pre>
</p>


<p><strong>More differences to Twelf syntax:</strong> We summarize a few more differences here. These restrictions may be addressed in the future.

<ul>
<li>Lambda-abstractions cannot be annotated with the type of the input. Writing <codeinline>\x:nat.x</codeinline> is illegal in Beluga.</li>
<li>Omitting the type in a Pi-type is illegal in Beluga. One cannot simply write <codeinline>({x}foo x T) -> foo E T'.</codeinline>. One must give the type of the variable <codeinline>x</codeinline>.</li>
<li>We do not at this point support arbitrary type annotations, i.e. one cannot write <codeinline>M : A</codeinline>. </li>
</ul>
</p>



<h2>Computation-level declarations</h2>
<p>
In addition to LF declarations, we support three additional kinds of declarations: schema declarations, which describe the structure of contexts, declarations of recursive functions (including mutual recursive functions), and  value declarations. 


<pre class="code">
c_decl ::= 
       | <keyword>schema</keyword> ctx_schema;            % schema declaration
       | rec_prog ;                    % recursive programs
       | <keyword>let</keyword> id = exp ;                % computation-level variable declaration


rec_prog ::= <keyword>rec</keyword> id : c_typ = exp      % recursive program id 
       | rec_prog <keyword>and</keyword> id : c_typ = exp 
</pre>
</p>
<p>
To embed LF terms into computations, we extend the LF term language to support contextual variables and projections on bound variables and contextual variables. These extensions cannot be used when declaring an LF signature, but only when we use LF objects within computation-level programs and declarations.

<pre class="code">
<comment>% Terms</comment>
term ::=  .. 
        | id . k               % k-th projection of a bound variable x
        | cvar sigma           % contextual variable    
        | <keyword>block</keyword> s_decl . term  % Sigma x:A.B

s_decl ::= id:term | s_decl , id_term

<comment>% Contextual variable</comment>

cvar   ::= 
        | id                % context variable, meta-variable 
        | #id               % parameter variable 
        | #id . k           % proj_k 

<comment>% Substitutions </comment>

sigma ::= 
      |                     % Nothing denotes the empty substitution
      | ..                  % Identity substitution
      | sigma term          % substitution sigma , M                      
</pre>
</p>

<p>We impose the following restrictions:
<ul>
  <li>Context variables always occur on their own (i.e. sigma is always empty).</li>
</ul>
</p>

<h2>Schemas and Contexts</h2>
<p>
Schemas classify context. We allow contexts to be built up not only by single declarations, but also by Sigma-types, i.e. dependent tuples. Logically, this means we can have not only atoms in our context, but also conjunctions of atoms (where the conjunction corresponds to the Sigma-type type-theoretically). 

<pre class="code">
<comment>% Schema</comment>
ctx_schema  ::= term | <keyword>some</keyword> [some_decls] <keyword>block</keyword> decls
some_decls  ::=                    % NOTHING
            | id:term              % Single declaration
            | id:_term, some_decls % Multiple declarations
decls       ::= . term             % Single declaration
            | {id:term}decls       % Multiple declarations  

<comment>% Context</comment>
ctx         ::=                    % NOTHING denoting the empty context
            | id                   % Context variable
            | x:block_type         % Context with one declaration x:block_type
            | ctx, id:block_type   % Psi, x:block_type  

block_type ::= term                % Single type
            | <keyword>block</keyword> decls          % Sigma-type.
</pre>
</p>

<p>
A <codeinline>ctx_hat</codeinline> is a context where we erase the type declarations. 
</p>
<pre class="code">
ctx_hat ::= 
        | id                      % Context variable or variable name
        |                         % NOTHING denoting the empty context
        | ctx_hat, id             % psihat, x 
</pre>



<h2>Computation-level types</h2>

<pre class="code">
<comment>% Computation-level Types</comment>
c_typ   ::= 
        | term [ctx]               % Contextual type A[Psi]
        | c_typ -> c_typ           % Functions
        | {id::term[ctx]} c_typ    % Dependent function type U::A[Psi]} tau
        | {id:ctx_schema} c_typ    % Implicit quantification over context variables 
        | {id:(ctx_schema)*} c_typ % Explicit quantification over context variables 

</pre>

<p>In the order of precedence, we disambiguate the syntax:
<ul>
  <li><codeinline>-></codeinline> is right associative.</li>  
  <li><codeinline>:</codeinline> is left associative.</li>  
  <li><codeinline>{}<codeinline> and <codeinline>\ </codeinline> are weak prefix operators. </li>  
  <li>Bound LF variables and constants must be written with lower-case letters.</li>  
  <li>Free meta-variables must be written with upper-case letters.</li>  
  <li>Context variables are written with lower-case letter.</li>
  <li>All LF terms need to be written in beta-normal form, i.e. no redeces can occur in terms. 
LF terms occurring as part of a contextual object must be written in eta-expanded form. For example, one must write <codeinline>[g] lam \x.E .. x</codeinline> and writing <codeinline>[g] lam E</codeinline> is currently rejected.
</li>  
   <li>In the contextual type <codeinline>A[Psi]</codeinline>, the type <codeinline>A</codeinline> must be a base type. This can always be achieved by translating a type <codeinline>(B -> A)[Psi]</codeinline> to <codeinline>A[Psi,x:B]</codeinline>. By applying this translation, any function type can be lowered. </li>
</ul>
</p>




<h2>Computation-level Expressions</h2>
<p>
<pre class="code">
exp    ::= 
       | [ctx_hat] term                % Contextual object: [g] M
       | [ctx] term                    % Contextual object where we provide
                                       % the full context as a type annotation
       | <keyword>FN</keyword> id => exp                  % Context abstraction FN g => e
       | <keyword>fn</keyword> id => exp                  % Computation-level functions: fn x => e 
       | <keyword>mlam</keyword> id => exp                % Abstraction over dependent type argument
       | exp exp                       % Application: e1 e2
       | exp [ctx]                     % Context application: e [g]
       | exp A &lt; ctx_hat . term &gt;      % Application of a dependent argument
       | exp A &lt; ctx . term &gt;          % Application of a dependent argument
                                       % with full context as type annotation
       | <keyword>let</keyword> [ctx] term = exp <keyword>in</keyword> exp   % Let expression
       | <keyword>case</keyword> exp <keyword>of</keyword> branch | .. | branch


<comment>% Branches</comment>
branch ::= 
       | delta  [ctx] term => exp
       | delta  [ctx] term : term[ctx] => exp   
                                       % with explicit type annotation on pattern

<comment>% Declarations of contextual variables</comment>
delta  ::= 
       |                               % NOTHING
       | {id::block_type[ctx]}         % Single contextual declaration
       | {id:(schema)*}                % Single context variables declaration
       | delta, {id::block_type[ctx]}  % Contextual declaration
       | delta, {id:(schema)*}         % Quantification over context variables
</pre>
</p>
<p>
The constructs <codeinline>{X::A[Psi]} tau</codeinline> the identifier <codeinline>X</codeinline> must be an upper-case
letter. It may overshadow other contextual variables in <codeinline>tau</codeinline>. Note that
<codeinline>A[Psi] -> tau</codeinline> is NOT just an abbreviation for <codeinline>{X::A[Psi]} tau </codeinline>.
where <codeinline>X</codeinline> does not appear in <codeinline>tau</codeinline>. On the computation-level we retain the
difference between quantification and implication. It is however worth
noting that there is a simple translation between programs of these
two types which involves lifting universally quantified elements to
become proper stand-alone computation-level expressions.
</p>


<p>In the order of precedence, we disambiguate the syntax and impose restrictions as follows:
<ul>
  <li>Juxtaposition (application) is left associative and has highest precedence.</li>
  <li>Bound variables and constants must be written with lower-case letters.</li>  
  <li>Free meta-variables must be written with upper-case letters.</li>  
  <li>All LF terms need to be written in beta-normal form, i.e. no redeces can occur in terms. LF terms occurring as part of a contextual object must be written in eta-expanded form. For example, one must write <codeinline>[g] lam \x.E .. x</codeinline> and writing <codeinline>[g] lam E</codeinline> is currently rejected.</li>   
</ul>
</p>


<h2>Emacs mode</h2>
<p>There is an emacs mode for Beluga, which is distributed with the Beluga code and example base. This will display the the source-syntax in a nicer format.
</p>

</div>
<div id="footer">
   <?php echoFile("../footer.html"); ?>
</div>

</div>
 

</body>
</html>
