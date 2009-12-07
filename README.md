Eregansu Administrative Interface
=================================

This isn’t so much an application as a base skin and simple router for other
code to build upon. Nexgenta modules built on Eregansu (cluster, mq, media, id, etc.) 
all use the code and CSS here as the basis for their own administrative interfaces.  

Others are, of course, welcome to do the same.

Check out a copy of this module into your app directory:

	cd app
	git clone git://github.com/nexgenta/admin.git
	
Create a symbolic link for the templates:

	cd ../templates
	ln -s ../app/templates admin

