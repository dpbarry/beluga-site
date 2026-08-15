<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
   <meta http-equiv="content-type" content="text/html; charset=utf-8" />
   <title>Beluga: Functional Programming with Higher-Order Abstract Syntax</title>
   <link rel="stylesheet" type="text/css" href="../style.css" />
</head>

<body>
<?php include "../functions.php"; ?>
<div id="wrapper">
<div id="header">
   <?php echoFile("../header.html"); ?>
</div>

<div id="main">

   <h1>Beluga: Functional Programming with Higher-Order Abstract Syntax</h1>

<p> Our main interest in this project is to investigate programming
and reasoning with data structures that provide support for
binders. Many object languages include binding constructs, and it is
striking that functional languages still lack direct support for
binders and common tricky operations such as renaming,
capture-avoiding substitution, and fresh name generation. </p>

<p> We advocate the use of <i>higher-order abstract syntax</i> (HOAS) where
we represent binders in the object language with binders in the
meta-language. One of the key benefits is that we not only get support
for renaming and fresh name generation, but also for capture-avoiding
substitution. While HOAS encodings have played an important role in
mechanizing the meta-theory of programming languages, it has been
difficult to incorporate HOAS encodings directly into functional
programming. </p>


<h2>Publications</h2> 

<ul>

<li>Joshua Dunfield and Brigitte Pientka.
    Case analysis of higher-order data.
    Manuscript submitted to TPHOLs '08.  February 2008.
    (<a href="tphols08-draft/abstract.html">abstract</a>)
    (<a href="tphols08-draft/Dunfield08_case-analysis-hoas.pdf">pdf</a>)
    (<a href="tphols08-draft/bibtex.bib">bibtex</a>)
</li>

<li>Brigitte Pientka and Joshua Dunfield.
    Functional programming with dependently-typed higher-order data.
    Manuscript submitted to LICS '08.  January 2008.
    (<a href="lics08-draft/abstract.html">abstract</a>)
    (<a href="lics08-draft/Pientka08_dependent-hoas.pdf">pdf</a>)
    (<a href="lics08-draft/bibtex.bib">bibtex</a>)
</li>

<li>Brigitte Pientka.
    A type-theoretic foundation for programming
    with higher-order abstract syntax and first-class substitutions.
    In
    <i>35th ACM Symposium on Principles of Programming Languages (POPL'08)</i>,
    pages 371-382.
    ACM Press, 2008.
    (<a href="popl08/abstract.html">abstract</a>)
    (<a href="popl08/Pientka08_hoasfun.pdf">pdf</a>)
    (<a href="popl08/bibtex.bib">bibtex</a>)
</li>

</ul>

<h2>Implementation</h2>

<p> We are actively developing a prototype implementation of our
language, Beluga, which supports programming with HOAS. The principal
implementor is Renaud Germain.  Source code and examples can be downloaded
<a href="beluga-simple.tar.gz">here</a>.</p>

<h2>People:</h2>
<ul>
<li><a href="http://www.cs.mcgill.ca/~bpientka">Brigitte Pientka</a></li>
<li><a href="http://www.cs.mcgill.ca/~joshua">Joshua Dunfield</a> (postdoc)</li>
<li><a href="http://www.cs.mcgill.ca/~rgerma">Renaud Germain</a> (Masters student)</li>
</ul>

</div>
<div id="footer">
   <?php echoFile("../footer.html"); ?>
</div>

</div>
</body>
</html>
