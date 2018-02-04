## **WP Patcher**

### **Install & activate**

1. Go to "**Plugins > Add New**".
2. Click on "**Upload Plugin**".
3.  Click on "**Browse**", locate in your system and select "**WpPatcher**" zip file.
4.  Click on "**Install Now**".
5.  When installation complete, click on "**Activate Plugin**".


For further information about WordPress plugins installation read in official [WordPress documentation](https://codex.wordpress.org/Managing_Plugins#Installing_Plugins).


***


### **Patch creation**
After plugin’s installation in admin menu will appear the item “**Code Patches**”.

1. Go to “**Code Patches >> Add new**”
2. Complete the fields of patch:
    * **Title**
    * **Description**   
    * **Patch for** ( choose if patch concerns a plugin or a theme )
    * **Plugin/Theme root folder**  ( insert the name of plugin’s or theme’s root folder )
    * **# Number ID** ( a unique patch ID )
    * **Files replaced** ( a zip file which includes the files that the patch  add or replace )

3. Save the patch.

After patch creation, you can go to “**Code Patches >> All Code Patches**” page, where appears all saved patches and their information.

From this list you are able to :

1. Visit the API endpoint which serves the data for the patch (column “**# Number ID**”).
2. Download patch (zip) file (column “**File**”).
3. Get the plugin’s/theme’s patcher code (column “**Patcher code**”).
4. In column “**Valid data**” you can see if any of following patch fields is not valid:
    + **Patch for**
    + **Plugin/Theme root folder**
    + **# Number ID**
    + **Files replaced**
5. Copy patcher's shortcode to use it in plugin/theme.

If any of these fields hasn’t value, the patch won’t be valid, it’s data won’t be served to any request and in patches list will appear the error messages ( in the appropriate columns ).


***


### **Patch ZIP file**

To be functional a patch, the ZIP file that will be uploaded in field “Files replaced” must have a specific structure.
The ZIP file must contain the files that need to be added or replaced to plugin/theme, by following plugin’s/theme’s structure.

For example, we have the theme “ExampleTheme” which have the following structure:

* ExampleTheme/
    * index.php
    * functions.php
    * styles.css
    * assets/
        * js/
            * script.js
            
If we want to replace file  “functions.php”, the ZIP file of patch must contain the following file:

* functions.php


If we want to replace file  “ExampleTheme/assets/js/script.js”, the ZIP file of patch must contain the following folders structure:

* /assets
    * js/
        * script.js


If we want to replace file  “ExampleTheme/assets/js/script.js” and add file “ExampleTheme/assets/css/bootstrap.css”, the ZIP file of patch must contain the following folders structure:

* /assets
    * css/
        * bootstrap.css
    * js/
        * script.js



***


### **Patcher code**
From the patches' list you can get the patcher code by clicking on the link in column “**Patcher code**”. 

Each code doesn’t concerns only one patch, but all the patches for each plugin/theme.

In case the patcher is for a theme, to use patcher code you have to copy the code in any place of theme’s file “**function.php**”.

In case the patcher is for a plugin, to use patcher code you have to copy the code in any place of plugin’s basic file ( the file that executed first when plugin is activated ).

When insert the patcher code in a plugin/theme you can use patcher shortcode to display the list of available plugin's/theme's patches in any page you want.

Don't forget to use the appropriate WordPress function to display the shortcode's content.

For example, if patcher shortcode is "[wp_patcher_exampleTheme]", to display it's content in PHP need to write:

<?php echo do_shortcode("[wp_patcher_exampleTheme]"); ?>


***


### **Patches endpoints**
With plugin’s activation are creating the following API endpoints that serve patches data:


* **{{ Site home url }}/wp/v2/patches/?site={{ requested domain url }}**

    Get all valid patches data.



* **{{ site home url }}/ wp/v2/patch/{{ patch Number ID }}/?site={{ requested domain url }}**

    Get specific (one) patch data, selected by patch “# Number ID”. 
    The parameter "**?site={{ requested domain url }}**" is not necessary if is not enabled patch option "Web site based Patch".



* **{{ site home url }} /wp/v2/patches/slug/{{ patch plugin’s/theme’s root folder tile }}/?site={{ requested domain url }}**

    Get specific (one) patch data, selected by plugin’s/theme’s root folder name.
    The parameter "**?site={{ requested domain url }}**" is not necessary if is not enabled patch option "Web site based Patch".



* **{{ site home url }}/wp/v2/patch/slug/code/{{ patch plugin’s/theme’s root folder tile }}/?site={{ requested domain url }}**

    Get the code that need to be placed in plugin’s/theme’s code, to enable patcher functionalities.
    The parameter "**?site={{ requested domain url }}**" is not necessary if is not enabled patch option "Web site based Patch".



* **{{ site home url }}/wp/v2/patch_file/{{ patch number id }}/?site={{ requested domain url }}**

    Get the (ZIP) file of a patch, selected by patch “# Number ID”.




