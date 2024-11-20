# MAGIC
Modern Approach to Generate Innovative Code
### b3tac0d3

Magic is designed in PHP using the OOP standards. It's designed to be completely modular so you can take what you want and leave what you don't need.


### Features

- Basic usage
- Routing
- View
- Controllers
- Models
- A.C.E.S. (Queries)
- ***SM:*** Short Mappings functions

---
---
### !!! STEP 1 !!!
#### BEFORE ANY USE CAN OCCUR, THE .htaccess FILE NEEDS TO BE UPDATED ON LINE 13. THE URL HAS TO BE THE BASE URL FOR YOUR SITE.

For live sites, this is usually www.yoursite.com but for localserver, this is going to be everything after localhost unless you're specifically using localhost to serve your site, in which case, it would just be localhost.

### !!! STEP 2 !!!
#### BEFORE ANY USE CAN OCCUR, THE config.php FILE NEEDS TO BE UPDATED.

The config.php file located in **src/app/config.php**, has 10 lines at top that need to be updated.

    $AppRootDir = "magic"; // root directory of the app, case sensitive
    $AppName = "Magic"; // App name as it should appear anywhere needed
    $LocalPort = "80"; // Local port. Can be left empty or default usually
    $MySQLPort = "3306"; // Local port. Can be left empty or default usually
    $AppUrl = "http://localhost"; // App base URL. Very important

    // MySQL variables. Set here so all changes are made at top of file.
    $MySQLHost = "localhost"; 
    $MySQLDbName = "magic";
    $MySQLUsername = "root";
    $MySQLPassword = "root";

    // Default page settings
    $PageTitle = "Magic"; // This is the default page title for all pages
    $Favicon = "src/user/img/framework/favicon_hat.ico";

### Basic Usage
#### App State
The app has 3 potential states: **Alpha / Beta / Live**

    - Alpha: Shows all errors, warnings and header alerting user to alpha mode.
    - Beta: Shows errors only with no warnings. Also, no header.
    - Live: No errors, no warnings, no default system header. Should 
    redirect any errors to a default error page.

**AppMode** can be changed in the **index.php** file. Default app mode is Alpha. The individual options can be changed in the config file under src/app/config/config.php. If the default options at the top of the file are set to "-1", they will default to whatever current app state setting is being used.

When using Alpha mode, the config file, session and cookies are reloaded everytime the page changes. This does not affect the user session for login purposes.

---

### Structure

The layout, dom and elements can be found under src/user/structure. All native app functionality is found under app. It's recommended to put all user specific functionality in the user folder under src/user.

### Layout File Options

**@yield** prints a view section to a specific spot in a layout

### View File Options
**@presto** is placed at the top of any view file that is going to use the presto syntax. It works exactly as shown.

    @presto

**@auth** is used to check a user permission or page access. This is placed at the top of the view page or can be used in the view function. This functionality is still being added. More updates to come on this for more granular authenticating of user roles, overrides and more.

    @auth(1) // Authorizes users will role 1 or greater

**@sess** is used to check a user session. This is separate from the session variables that are stored for back end use. This function will specifically check if the user is logged in or not.

    @sess

**@layout** defines a layout file to use when the view is called.

    @layout("layout_name") // Very important " or ' required

**@section** starts a section that will be used in the layout file. Not all sections are mandatory. If the layout file allows for 10 sections, you can use as few or as many as you want.

    @section("section_name") // Very important " or ' required

**@endsection** ends the processing of the current section.

    @endsection // No ()

**@require** gets a file with `require_once`. Can be used multiple times. Will throw errors when there are directory errors. Using the .php extension is not required for this function but is allowed.

    @require("require_name") // Very important " or ' required

**@include** gets a file with `include_once`. Can be used multiple times. Will throw errors when there are directory errors. Using the .php extension is not required for this function but is allowed.

    @include("include_name") // Very important " or ' required

### Views / Controllers / Models

#### Short Variables
Short variables can be insterted in a view to pass data or use built in functionality. There are currently 4 options. All short variables are surrounded by "{{}}"

You can reach short mappings as such

#### This would print out the directory path for the PHP by using the sm functions in short mapping

    {{Dir::Php}}

#### This would print out the Url path for the PHP by using the sm functions in short mapping

    {{Url::Php}}

#### This can access the custom session variables under `$_SESSION["Root"]` while use the dot syntax to separate keys. This can go as deep as you want in to any array in the root values.

    {{Cus::App.PageTitle}} // $_SESSION["Root"]["App"]["PageTitle"]

#### Later we'll be adding a separate short variable for base session variables using the following syntax. This is not currently active or useable.

    {{Ses::VarName}} // NOT CURRENTLY WRITTEN IN

#### This would look for a custom variable named content that was passed to the view. If it is found, the data would populate here. If not, the variable would be removed and the section left empty

    {{Content}}

They can also be used in all php functions statically as follows 

    sm::Dir(x)
    sm::Url(x)
    sm::Cus(x, "App") // Default is app but can access anything in root array
    sm::Dep(Aces) // Access dependency directories directly

Root array keys are as follows. They can be studied in the config.php file.

    $_SESSION["Root"]
        [Dev] // $DevArray,
        [App] // $AppArray,
        [Paths] // $PathsArray,
        [Depends] // $DependsArray,
        [Db] // $DbArray

#### Shorts Variables Available for use
1. Base  
2. Src  
3. User  
4. App  
5. Routes  
6. Views  
7. Controllers  
8. Models  
9. Css  
10. Js  
11. Php  
12. Forms  
13. Img  
14. Plugins  
15. Depends  
16. Logs  
17. Build  
18. Dom  
19. Nav  
20. Layouts  



### A.C.E.S.
#### Automated Concise Effortless SQL
### D.E.C.K.S.
#### Dynamic Error-handling and Code Kit System
### F.L.O.P.S.
#### Fast Logging and Output Processing System
### F.O.L.D.S.
#### Form Object Library and Designer System
### S.P.A.D.E.S.
#### Streamlined PHP AJAX Development Engine Software