#Reevio - Bringing Blogging to Tent

[Reevio](https://reevio.tent.is "Reevio on Tent") is a self-hosted blogging platform based on the [Tent protocol](http://tent.io "Tent - The decentralized social web") that uses Tent's Essay post-types to share your content with the world.

##Features
Reevio is a blog-front end that anybody can download and install on their server. Reevio allows you to blog via your Tent server; it displays the Essay posts from your Tent server (for example, [^daniel.tent.is](https://daniel.tent.is "Daniel Siders, Tent co-founder") or [^jeena.net](https://jeena.net "Jeena, one of the first Tent self-hosters")) in a blog format so that anybody (permissions permitting) can read them with ease; it also has a single-Essay view so your readers can focus on just one post. Reevio also provides your readers an RSS feed for your Essay posts, as well as details from your profile and a stream of your latest status posts in the sidebar and on the profile page.

##Contributions
We would greatly appreciate any contributions you would like to make to the project. This includes opening an issue if you find a bug or have an idea for a feature, contributing to our wiki to help people that are new to Reevio get their bearings, and making pull requests if you would like to help us build and extend the software. If you like Reevio, feel free to spread the word about it and help both Reevio and Tent grow!

##Users
We'd like to know if you're using Reevio! If you have decided to use our software, make a pull request with your name, tent entity, and blog link added to the list below in the following format: ```* User's Name, Blog: [blog.url.com](http://blog.url.com "Short description of blog and/or yourself.")```. Below is the list of current users of Reevio.

* &! (bitandbang), Blog: [bnb.im](http://bnb.im "Web-driven college student who loves Tent."), Tentity: ^[bnb.tent.is](https://bnb.tent.is "Tent Entity.")
* Cacauu, Blog: [cacauu.de](http://cacauu.de "German technonlogy-lover who blogs about technology, coding, photography and some other things"), Tentity: ^[cacauu.tent.is](https://cacauu.tent.is "Tent Entity.")
* Reevio, Blog: [Official Reevio Blog](http://cacauu.de/reevio), Tentity: ^[reevio.tent.is](https://reevio.tent.is)
* shawnj, Blog: [r.shawnj.co](http://r.shawnj.co "Person"), Tentity [^shawnj.tent.is](https://shawnj.tent.is)

##Installation

###1. Move into the folder you'd like to install Reevio in 

	cd your/reevio/path

###2. Clone the git repo

	git clone https://github.com/reevio/reevio.git

###3. Install Dependencies

#### If you already have Composer

	composer install
			
#### If you need Composer

	curl -s https://getcomposer.org/installer | php
	php composer.phar install
			
###4. Rename app.ini.sample to app.ini

###5. Fill in your information in app.ini

##System Requirements

To install and run Reevio you need a server running PHP 5.3.2 or higher. 

##Change the style

If you want to change the style of Reevio, the easiest way to do that is to change the things in ```css/light.css``` because all the tags, classes and ids used in Reevio can be seen and edited there. There's only one thing you have to remember: By default, Reevio uses ```css/light.min.css``` to make the page load even faster, so all the changes you do to light.css won't be displayed anywhere. If you want to change your style, open ```views/Core/layout.twig``` and change the source from the stylesheet from ```css/light.min.css``` to ```css/light.css```.
If you want to use a minified stylesheet with your changes after that again, I recommend you to use [Smaller](http://smallerapp.com/) on your Mac and change the source from the stylesheet on layout.twig back again.

##Contact

If you have any questions, ideas or problems, contact Yannik on [Tent](https://cacauu.tent.is).
