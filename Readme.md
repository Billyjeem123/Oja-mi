PHP Boilerplate v0.3
____
## Getting started

To use this boilerplate, follow these steps:

* Clone the repository to your local machine.
* Install PHP and a web server (such as Apache) if you haven't already.

* Import the included database dump file inside the db folder (boiler.sql), you  can rename it as you see fit. 
* In the boiler plate there is an .env file, inside this file, register your constants inside  the .env. there are some predefined .env variables which are already defined.
* Please note that this boilerplate uses PDO for database connectivity. Additionally, we advice that you unset any instantiated classes once they are no longer needed, in order to free up memory and improve performance
* Please note that in this boiler plate,   a mail design has been created. you can modify it to suite your taste

---
## Authentication

* This boilerplate uses an (Authorization header) to validate API requests. The Authorization header must be included in all API requests for the request to be processed.
 
* To include the Authorization header, make sure to add it to the header section of your API request. Here is an example using cURL


---
## Configuration Update

* The boilerplate has been reconfigured, so you no longer need to include the .php extension when testing  endpoint on any  tools such as Postman and co
---
## Functionality

* This boilerplate comes pre-loaded with user authentication functionality, including login, registration, and password reset. However, if you need to modify or add any fields to the existing functionality, you can easily do so by modifying the appropriate modules.

* Additionally, this boilerplate is designed to be modular and extensible, so you can add new features and functionality as needed. For example, if you need to add social media login capabilities, you can create a new module for that and integrate it with the existing authentication functionality.

---
## Customization
`
If you need to add or modify functionality in this boilerplate, you can do so by editing the existing code or adding your own files. The code is organized into separate files for each major feature, so it should be easy to find and modify the relevant code.

---
## Documentation
`
To learn more about how this boilerplate works and how to use it, please refer to the "docx" folder included in the boiler  (see docx.txt for details). Inside this folder, you will find a postman collection file that you can download and import into Postman. This file contains detailed information on the existing endpoint  of the code and how to customize it to suit your needs.

---
## Database
`
The database used by this boilerplate is stored in the docx folder, insode the folder there is  folder that reads db, the sql file is located inside the DB folder. You can import this file into your local database server to create the necessary tables and data. If you need to modify the database schema or add new data, you can do so by editing this file or by using a tool like phpMyAdmin to modify the tables directly.

---
## Autoloading System
`
In the documentation, it should be mentioned that the boilerplate uses the PSR-4 autoloading system, which is a standard established by the PHP Framework Interop Group (PHP-FIG). The PSR-4 standard defines rules for autoloading classes from file paths based on their namespace.


* It is important to note that the name of the module must also be the same as the name of the class in order for the PSR-4 autoloading system to work properly. This means that when creating a new module, the module name and class name should be identical.


* Class names at any depth  hierarchy correspond to file names in the base directory, with the .php extension must be  added.

---
## Contact Developer
`
If you have any questions or encounter any issues while using this boilerplate, please feel free to contact  billycodes @ 2348117283226 . We will do our best to respond to your inquiries as soon as possible. Additionally, if you would like to countribute or  suggest improvements, please don't hesitate to reach out to us. We welcome any feedback and collaboration to make this boilerplate even better.


