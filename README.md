# Using Clinker in a server

[Clinker](https://github.com/gamcil/clinker) is a great tool to visualize sequence alignments. It generates an interactive figure in a HTML page, which is loaded locally. In some settings, it is useful to be able to access Clinker directly from a server, so t he user doesn't have to install Clinker locally. One such setting is a classroom, where multiple students need to use Clinker to visualize an alignment. It would be easier to just run Clinker on a server and connect via an url.

I started this project to solve the previous issue. This project adds some PHP and JavaScript code to be able to access Clinker via remote server. I have found this solution very useful, and perhaps other people will! This project is in an early stage. Contributions are welcome.

To use it, just git clone the repo into the root directory of your web server. Then add the path to the clinker executable in your server to the global variable "clinker_path".

Cheers,
Semidán

