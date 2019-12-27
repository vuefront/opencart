<p align="center">
  <br>
  <a href="https://vuefront.com">
    <img src="https://raw.githubusercontent.com/vuefront/vuefront-docs/master/.vuepress/public/img/github/vuefront-opencart.jpg" width="400"/>
  </a>
</p>
<h1 align="center">VueFront</h1>
<h3 align="center">CMS Connect App for OpenCart 2.x-3.x
</h3>

<p align="center">
  <a href="https://github.com/vuefront/vuefront"><img src="https://img.shields.io/badge/price-FREE-0098f7.svg" alt="Version"></a>
  <a href="https://discord.gg/C9vcTCQ"><img src="https://img.shields.io/badge/chat-on%20discord-7289da.svg" alt="Chat"></a>
</p>

<p align="center">
Show your :heart: - give us a :star: <br/> 
Help us grow this project to be the best it can be!
  </p>


__VueFront__ is a <a href="//vuejs.org">VueJS powered</a> CMS agnostic SPA & PWA frontend for your old-fashioned Blog and E-commerce site. 

__OpenCart__ - Open-source eCommerce platform built with MVC pattern.

__CMS Connect App__ - adds the connection between the OpenCart CMS and VueFront Web App via a GraphQL API.

## DEMO

[VueFront on OpenCart](https://opencart.vuefront.com/)

![VueFront CMS Connect App](http://joxi.net/krDlvPdfKO5P9r.jpg)

## OpenCart Versions
This repo stores the codebase for the CMS Connect App for OpenCart. Because of OpenCart's versioning, the branches are structured as follows 

| Repo Branch | OpenCart Versions  |
|--------|-------------|
| [master](https://github.com/vuefront/opencart) | 2.x-3.x     |
| [1.5x](https://github.com/vuefront/opencart/tree/1.5x)   | 1.5.x       |

### OpenCart Blog 
Since OpenCart does not have a built-in Blog, we use the [Free Blog Module](https://github.com/Dreamvention/2_d_blog_module) by Dreamvention for version 2.x-3.x

## How to install?
Php version required >= 5.5, <= 7.2 (this limitation will be removed in the future)

### Quick Install
1. [Download](https://github.com/vuefront/opencart/releases) the **compiled** Extensions from the latest releases. 
2. Upload via OpenCart Admin -> Extension Installer
3. Go to Extensions -> Modules -> VueFront and click install
4. Click edit to view copy the CMS Connect URL

You will need the CMS Connect URL to complete the [VueFront Web App installation](https://vuefront.com/guide/setup.html)

### Advanced Install
The official compiled version of the CMS Connect APP includes other supporting extensions such as d_opencart_patch and d_twig_manager. 

You can download the source code from the master branch directly and upload via ftp to your root folder. When activating the module, you should have the following extensions preinstalled: d_opencart_patch, d_twig_manager, d_twig (only for 2.x)

You can also install the d_blog_module to add blog features to VueFront. 

### Install via Shopunity
If you have shopunity module installed, you can use that for a super quick installation:
1. go to OpenCart admin -> shopunity -> marketplace tab
2. search for VueFront
3. Click install.

You can also install the d_blog_module via Shopunity as well. 

## Deploy to hosting (static website)
### via VueFront Deploy service (recommended)
1. Install the VueFront CMS Connect App from this repo.
2. Log in or register an account with VueFront.com
3. Build your first Web App
4. Activate the new Frontend Web App (only avalible for Apache servers)
 > For Nginx you need to add this code to your `nginx.config` file right after the `index` directive
 ```
location ~ ^((?!image|.php|admin|catalog|\/img\/.*\/|wp-json|wp-admin|wp-content|checkout|rest|static|order|themes\/|modules\/|js\/|\/vuefront\/).)*$ {
    try_files /vuefront/$uri /vuefront/$uri "/vuefront${uri}index.html" /vuefront$uri.html /vuefront/200.html;
}
 ```
 

### via ftp manually
1. Via Ftp create a new folder `vuefront` in the root of your OpenCart site on your hosting. 
2. Build your VueFront Web App on you local computer ([read more](https://vuefront.com/guide/setup.html)) 
```
yarn generate
```
3. Copy all files from folder `dist` to the newly created `vuefront` folder
4. modify you `.htaccess` file by adding after `RewriteBase` rule the following rules:
```htaccess
# VueFront scripts, styles and images
RewriteCond %{REQUEST_URI} .*(_nuxt)
RewriteCond %{REQUEST_URI} !.*/vuefront/_nuxt
RewriteRule ^([^?]*) vuefront/$1
# VueFront sw.js
RewriteCond %{REQUEST_URI} .*(sw.js)
RewriteCond %{REQUEST_URI} !.*/vuefront/sw.js
RewriteRule ^([^?]*) vuefront/$1
# VueFront favicon.ico
RewriteCond %{REQUEST_URI} .*(favicon.ico)
RewriteCond %{REQUEST_URI} !.*/vuefront/favicon.ico
RewriteRule ^([^?]*) vuefront/$1
# VueFront pages
# VueFront home page
RewriteCond %{REQUEST_URI} !.*(image|.php|admin|catalog|\/img\/.*\/|wp-json|wp-admin|wp-content|checkout|rest|static|order|themes\/|modules\/|js\/|\/vuefront\/)
RewriteCond %{QUERY_STRING} !.*(rest_route)
RewriteCond %{DOCUMENT_ROOT}".$document_path."vuefront/index.html -f
RewriteRule ^$ vuefront/index.html [L]
RewriteCond %{REQUEST_URI} !.*(image|.php|admin|catalog|\/img\/.*\/|wp-json|wp-admin|wp-content|checkout|rest|static|order|themes\/|modules\/|js\/|\/vuefront\/)
RewriteCond %{QUERY_STRING} !.*(rest_route)
RewriteCond %{DOCUMENT_ROOT}".$document_path."vuefront/index.html !-f
RewriteRule ^$ vuefront/200.html [L]
# VueFront page if exists html file
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteCond %{REQUEST_URI} !.*(image|.php|admin|catalog|\/img\/.*\/|wp-json|wp-admin|wp-content|checkout|rest|static|order|themes\/|modules\/|js\/|\/vuefront\/)
RewriteCond %{QUERY_STRING} !.*(rest_route)
RewriteCond %{DOCUMENT_ROOT}".$document_path."vuefront/$1.html -f
RewriteRule ^([^?]*) vuefront/$1.html [L,QSA]
# VueFront page if not exists html file
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteCond %{REQUEST_URI} !.*(image|.php|admin|catalog|\/img\/.*\/|wp-json|wp-admin|wp-content|checkout|rest|static|order|themes\/|modules\/|js\/|\/vuefront\/)
RewriteCond %{QUERY_STRING} !.*(rest_route)
RewriteCond %{DOCUMENT_ROOT}".$document_path."vuefront/$1.html !-f
RewriteRule ^([^?]*) vuefront/200.html [L,QSA]
```

 > For Nginx you need to add this code to your nginx.config file right after the index rule
 ```
location ~ ^((?!image|.php|admin|catalog|\/img\/.*\/|wp-json|wp-admin|wp-content|checkout|rest|static|order|themes\/|modules\/|js\/|\/vuefront\/).)*$ {
    try_files /vuefront/$uri /vuefront/$uri "/vuefront${uri}index.html" /vuefront$uri.html /vuefront/200.html;
}
 ```
 
## Support
For support please contact us at [Discord](https://discord.gg/C9vcTCQ)

## Submit an issue
For submitting an issue, please create one in the [issues tab](https://github.com/vuefront/vuefront/issues). Remember to provide a detailed explanation of your case and a way to reproduce it. 

Enjoy!
