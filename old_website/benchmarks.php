<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
   <meta http-equiv="content-type" content="text/html; charset=utf-8" />
   <title>Beluga: Functional Programming with Higher-Order Abstract Syntax</title>
   <link rel="stylesheet" type="text/css" href="../tutorial-style.css" />
</head>


<body>
<?php include "../functions.php"; ?>
<div id="wrapper">
   <?php echoFile("header.html"); ?>
  <div id="menu">
   </div>
<div id="main">

 <h1>Benchmarks</h1>

<p>We present several benchmark problems. All these examples are
purposefully simple, so they can be easily understood and one can
quickly appreciate the capabilities and trade-offs different systems
offer. Yet we believe they are representative for issues and problems
arising when formalizing formal systems and proofs about them. All of
them revolve around the lambda-calculus. </p>

<p>A longer discussion on this can be found in <a href="submitted/worlds.pdf">Reasoning
with Higher-Order Abstract Syntax and Contexts: A Comparison</a> by
Amy Felty and Brigitte Pientka.
</p>

<h2>Solutions implemented in the Twelf system </h2>
<p>
<ul>
<li>Reasoning about declarative and algorithmic equality 
    (<a href="twelf-solutions/eq.cfg">config file</a>, 
     <a href="twelf-solutions/equal.elf">elf file</a>)
</li>
<li>Subject reduction for simply typed lambda-calculus
    (<a href="twelf-solutions/subject-red.cfg">config file</a>, 
     <a href="twelf-solutions/subject-red.elf">elf file</a>)
</li>
<li> Type unicity
     (<a href="twelf-solutions/unique.cfg">config file</a>, 
     <a href="twelf-solutions/unique.elf">elf file</a>)</li> 
<li> Reasoning about shapes of lambda-terms
    (<a href="twelf-solutions/shapes.cfg">config file</a>, 
     <a href="twelf-solutions/shapes.elf">elf file</a>)
</li>
</ul>
</p>

<h2>Solutions implemented in Beluga </h2>
<p> Most of the challenge problems are discussed in the <a href="tutorial.php">tutorial</a> for Beluga. We list them individually below.
<ul>
<li>Reasoning about declarative and algorithmic equality 
    (<a href="beluga-solutions/equal-proof.bel">beluga file</a>)
<br> see also Tutorial <a href="eq-example.php">4. Equality reasoning</a>
</li>
<li>Subject reduction for simply typed lambda-calculus
    (<a href="beluga-solutions/subject-red.bel">beluga file</a>) <br>
     see also Tutorial <a href="tps-example.php">3. Subject reduction</a>
</li>
<li> Type unicity     (<a href="beluga-solutions/unique.bel">beluga file</a>) <br>
     see also Tutorial <a href="uniqueness-example.php">1. Type uniqueness</a>
</li> 
<li> Reasoning about shapes of lambda-terms (<a href="beluga-solutions/shapes.bel">beluga file</a>) </li> 
<li> Path example  (<a href="beluga-solutions/path.bel">beluga file</a>) <br>
     see also Tutorial <a href="path-example.php">2. Reasoning about paths in lambda-terms</a>
</li> 
<li> Reasoning about "smaller" relation on lambda-terms (<a href="beluga-solutions/struct-smaller.bel">beluga file</a>) <br>
     see also Tutorial <a href="struct-smaller.php">5. Reasoning about "smaller" relation on lambda-terms</a>
 </li>
</ul>
</p>



<h2>Solutions in two-level Hybrid </h2>
<p>Please see <a href="http://www.site.uottawa.ca/~afelty/HybridCoq/">Two-level Hybrid: Case studies</a>.</p>

<p>
</p>
</div>
<div id="footer">
   <?php echoFile("../footer.html"); ?>
</div>

</div>
 

</body>
</html>
