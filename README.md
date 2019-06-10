# VueFront CMS Connect App for OpenCart

https://vuefront.com/

VueFront - Vue powered agnostic frontend web app for your old fashioned Blog and Ecommerce site. 

OpenCart - Open-source eCommerce platform built with MVC pattern.

CMS Connect App - adds the connection between the OpenCart CMS and VueFront WebApp via a GraphQL API.

## DEMO

[VueFront](https://opencart.vuefront.com/)

[Admin](https://opencart.vuefront.com/admin)

## OpenCart Versions
This repo stores the codebase for the CMS Connect App for OpenCart. Because of OpenCart's versioning, the branches are structured as follows 

| Repo Branch | OpenCart Versions  |
|--------|-------------|
| master | 2.x-3.x     |
| 1.5x   | 1.5.x       |

### OpenCart Blog 
Since OpenCart does not have a built-in Blog, we use the [Free Blog Module](https://github.com/Dreamvention/2_d_blog_module) by Dreamvention for version 2.x-3.x

## How to install?

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

## Support
For support please contact us a https://dreamvention.ee/support 

## Submit an issue
For submiting an issue, please create one in the [issues tab](https://github.com/vuefront/opencart/issues). Remeber to provide a detailed explonation of your case and a way to reproduce it. 

Enjoy!
