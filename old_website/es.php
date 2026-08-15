<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
   <meta http-equiv="content-type" content="text/html; charset=utf-8" />
   <title>Beluga: Functional Programming with Higher-Order Abstract Syntax</title>
   <link rel="stylesheet" type="text/css" href="../t.css" />
</head>

<body>
<?php include "../functions.php"; ?>
<div id="wrapper">
   <?php echoFile("header.html"); ?>
  <div id="menu">
   </div>
<div id="main">

<h1><a href="tutorial.php">Tutorial</a></h1>
<h3><a href="uniqueness-example.php">1. Type uniqueness</a></h3>
<h3><a href="path-example.php">2. Reasoning about paths in lambda-terms</a></h3>
<h3><a href="tps-example.php">3. Subject reduction</a></h3>
<h3><a href="eq-example.php">4. Equality reasoning</a></h3>
<h3>5. Reasoning about a "smaller" relation on lambda-terms</h3>

<p>This simple example illustrates the expressive power of Beluga. It is simpler than the
 example where we reason about paths in a lambda term. To simplify matters even further, this example uses untyped lambda-terms. They are defined as before, except that lambda-abstractions do not have type annotations.
</p>
<p>
We begin by defining when a lambda-term is a subterm of another lambda-term. The type family
<span class="codeinline">lt</span> defines when a term is strictly smaller than another term,
and <span class="codeinline">eq</span> defines when two terms are equal. <span class="codeinline">le</span>
succeeds if a term is strictly smaller or equal to another term. 
</p>


<pre class="code">
eq: exp &rarr; exp &rarr; type.
e_ref: eq T T.

lt: exp &rarr; exp &rarr; type.
le: exp &rarr; exp &rarr; type.

lt_lam: ({x:exp}le N (M x)) &rarr; lt N (lam M).
lt_appl: le N M1 &rarr; lt N (app M1 M2).
lt_appr: le N M1 &rarr; lt N (app M1 M2).


le_= : eq M N &rarr; le M N.
le_&lt; : lt M N &rarr; le M N.
</pre>

<p>We want to prove the following theorem:
</p>

<p>
<strong>Theorem:</strong>
If, for all  <span class="codeinline">M</span>,  &#x22A2; <span class="codeinline">le M N</span>  implies  &#x22A2; <span class="codeinline">le M K</span>,
then   &#x22A2; <span class="codeinline">le N K</span>. 
</p>

<p>This is a simple example to illustrate the expressive power of Beluga. Because the statements require nested quantification and implication, it is not possible to state this theorem directly in some systems such as the Twelf system.
</p>

<p>We will state here a more general version which allows the context to be non-empty.
</p>
<p>
<strong>Lemma:</strong>
If, for all <span class="codeinline">M</span>,  &Gamma; &#x22A2; <span class="codeinline">le M N</span>  implies  &Gamma; &#x22A2; <span class="codeinline">lt M K</span>,
then  &Gamma; &#x22A2; <span class="codeinline">le N K</span>. 
</p>


<p>The context &Gamma; contains assumptions <span class="codeinline">x:exp</span>. 
</p>

<pre class="code">
schema ctx = exp ; 
</pre>




<h3>On paper proof:</h3>

<p>To prove the above theorem, we proceed as follows: Assume that
"for all <span class="codeinline">M</span>,   &Gamma; &#x22A2; <span class="codeinline">le M N</span>  implies  &Gamma; &#x22A2; <span class="codeinline">lt M K</span>".
Use <span class="codeinline">N</span> to instantiate this assumption and eliminate the
universal quantifier. Hence, we obtain:  &Gamma; &#x22A2; <span class="codeinline">le N N</span>
implies  &Gamma; &#x22A2; <span class="codeinline">le N K</span>.  By reflexivity of equality,
using rule <span class="codeinline">e_ref</span>, we know that <span class="codeinline">eq N N</span>.
By the rule <span class="codeinline">le_=</span>, we conclude <span class="codeinline">le N N</span>.
Hence, we can now use implication elimination and conclude that <span class="codeinline">le N K</span>,
which is what we needed to prove.
</p>


<p>
The proof for the stated lemma can be implemented as a function which has the following type:
</p>

<pre class="code">
{g:(ctx)*}({M::exp[g]} (le (M <i>..</i>) (N <i>..</i>))[g] &rarr; (le (M <i>..</i>) (K <i>..</i>))[g])
      &rarr; (le (N <i>..</i>) (K <i>..</i>))[g]  
</pre>

<p>
The type corresponds directly to the specified lemma where <span class="codeinline">{g:(ctx)*}</span> introduces quantification over the context variable <span class="codeinline">g</span> which has schema <span class="codeinline">ctx</span>. 
Meta-variables written with curly braces such as <span class="codeinline">{M::exp[g]}</span>,<span class="codeinline">{N::exp[g]}</span> correspond to universal quantification over the variables <span class="codeinline">M</span>. 
</p>


<p> The proof can be implemented as a function in Beluga following closely the more informal
development given earlier. We treat computation-level expressions that correspond
to quantifier introduction and elimination differently from the expressions
corresponding to implication introduction and elimination. To introduce a universally
quantified meta-variable <span class="codeinline">M</span>, we write
<span class="codeinline"><keyword>mlam</keyword> M &rArr; ...</span>.
To eliminate a universal quantifier, we write <span class="codeinline">e &lt;g.M&gt;</span>
where <span class="codeinline">e</span> has type <span class="codeinline">{M::A[g]}.tau</span> and
<span class="codeinline">g.M</span> describes the data-level object used to instantiate the
universal quantifier and has type <span class="codeinline">A</span> in the context
<span class="codeinline">g</span>. For implication introduction, we write
<span class="codeinline"><keyword>fn</keyword> x &rArr; ...</span>. 
</p>

<pre class="code">
<keyword>rec</keyword> lemma : {g:(ctx)*}({M::exp[g]} (le (M <i>..</i>) (N <i>..</i>))[g] &rarr; (le (M <i>..</i>) (K <i>..</i>))[g])
      &rarr; (le (N <i>..</i>) (K <i>..</i>))[g]  
 = 
<keyword>&Lambda;</keyword> g &rArr; <keyword>fn</keyword> f &rArr; f &lt; g. _ &gt; ([g] le_= e_ref) ;


</pre>

<p>In the code we write <span class="codeinline">_</span> and let type reconstruction fill in the correct instantiation.
</p>


<p>
</p>
</div>
<div id="footer">
   <?php echoFile("../footer.html"); ?>
</div>

</div>
 

</body>
</html>
