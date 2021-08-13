# KWCMS3 - kwcms_core

------------

Basic idea about KWCMS3 framework and its modular system. It contains basic tree in which
the application should exists.

A many things has been ported from KWCMS1 and used here and many are brand new as necessity
calls.

The main thing about KWCMS framework is the orientation on file tree and controller tree.
I already has too many problems with usual frameworks which did not acknowledged these two
important parts of web life. Another thing is a hard way structured inputs and outputs.
Usually you get only output in format which results plaintext and input in PHP vars. Not here.
Here you get larger control over the input and output. Object way. This makes testing process
more simple. For both modules and libraries. Most of libraries are in separated project where
cou can run them extra and contains simple tests. But the basics is here, in this project.

This is only example of work with KWCMS3, it should be treated that way.
