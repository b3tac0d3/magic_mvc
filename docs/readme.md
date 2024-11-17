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


### Basic Usage
#### App State
The app has 3 potential states: **Alpha / Beta / Live**

    - Alpha: Shows all errors, warnings and header alerting user to alpha mode.
    - Beta: Shows errors only with no warnings. Also, no header.
    - Live: No errors, no warnings, no default system header. Should 
    redirect any errors to a default error page.

AppMode can be changed in the index.php file. Default app mode is Alpha. The individual options can be changed in the config file under src/app/config/config.php. If the default options at the top of the file are set to "-1", they will default to whatever current app state setting is being used.

When using Alpha mode, the config file, session and cookies are reloaded everytime the page changes. This does not affect the user session for login purposes.

---

#### Short Variables
Short variables can be insterted in a view to pass data or use built in functionality. There are currently 4 options. All short variables are surrounded by "{{}}"

You can reach short mappings as such

#### This would print out the directory path for the PHP by using the sm functions in short mapping

    {{Dir::Php}}

#### This would print out the Url path for the PHP by using the sm functions in short mapping

    {{Url::Php}}

#### This can access the custom session variables under 
`$_SESSION["App"]["Root"]["Custom"]["UserVariableHere"]`

    {{Cus::Php}}

#### This would look for a custom variable named content that was passed to the view. If it is found, the data would populate here. If not, the variable would be removed and the section left empty

    {{Content}}

### Structure

The layout, dom and elements can be found under src/app/structure. All native app functionality is found under app. It's recommended to put all user specific functionality in the user folder under src/user.

### Layout File Options

**@yield** prints a view section to a specific spot in a layout

### View File Options
**@presto** is placed at the top of any view file that is going to use the presto syntax. 

**@auth** is used to check a user permission or page access. This is placed at the top of the view page or can be used in the view function. This functionality is still being added. More updates to come on this.

**@sess** is used to check a user session. This is separate from the session variables that are stored for back end use. This function will specifically check if the user is logged in or not.

**@layout** defines a layout file to use when the view is called.

**@section** starts a section that will be used in the layout file. Not all sections are mandatory. If the layout file allows for 10 sections, you can use as few or as many as you want.

**@endsection** ends the processing of the current section.

**@require** gets a file with `require_once`. Can be used multiple times. Will throw errors when there are directory errors. Using the .php extension is not required for this function but is allowed.

**@include** gets a file with `include_once`. Can be used multiple times. Will throw errors when there are directory errors. Using the .php extension is not required for this function but is allowed.

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