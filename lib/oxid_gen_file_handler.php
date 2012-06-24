<?php

class OxidGenFileHandler extends CLI {

  protected $vendor = "";
  protected $namespace = "";
  protected $id = "";

  protected $class_template = "templates/class_template.php";
  protected $model_template = "templates/model_template.php";
  protected $controller_template = "templates/controller_template.php";
  protected $extend_template = "templates/extend_template.php";

  protected $folder_mode = 0777;

  protected $controller_suffix = "_controller";
  protected $model_suffix = "";
  protected $lib_suffix = "_lib";

  protected $defaultReplacementArray = array( "class_meta_data_license" => "" );


  public function __construct( $initialize = true ) {
    parent::__construct( false );
  }

  public function setId( $id = "" ) {
    $this->id = $id;
  }

  public function setNamespace( $namespace = "" ) {
    $this->namespace = $namespace;
  }

  public function setVendor( $vendor = "" ) {
    $this->vendor = $vendor;
  }


  public function getModulePath( $fullpath = true ) {
    if ( $this->vendor != "" ) {
      $path = "modules/" . $this->vendor . "/" . $this->id;
    }else {
      $path = "modules/" . $this->id;
    }
    if ( $fullpath ) {
      return getShopBasePath() . $path . "/";
    }else {
      return $path . "/";
    }
  }

  public function copyThumbnail( $thumbnail_file = "", $module_path = "" ) {

    if ( $thumbnail_file == "default.png" ) {

    }
  }

  public function createControllerClass( $folder, $class_name ) {
    $class_name = $this->namespace . $class_name . $this->controller_suffix;
    $file_class_name = $this->formatClassname( $class_name ) . ".php";
    $new_file = $this->copyFile( $folder, $file_class_name, $this->controller_template );
    if ( $new_file ) {
      $this->makeControllerClass( $new_file, $class_name );
    }
    return $new_file;
  }

  public function createModelClass( $folder, $class_name ) {
    $class_name = $this->namespace . $class_name . $this->model_suffix;
    $file_class_name = $this->formatClassname( $class_name ) . ".php";
    $new_file = $this->copyFile( $folder, $file_class_name, $this->model_template );
    if ( $new_file ) {
      $this->makeModelClass( $new_file, $class_name );
    }
    return $new_file;
  }

  public function createLibClass( $folder, $class_name ) {
    $class_name = $this->namespace . $class_name . $this->lib_suffix;
    $file_class_name = $this->formatClassname( $class_name ) . ".php";
    $new_file = $this->copyFile( $folder, $file_class_name, $this->class_template );
    if ( $new_file ) {
      $this->makeLibClass( $new_file, $class_name );
    }
    return $new_file;
  }

  public function createClass( $folder, $class_name ) {
    $class_name = $this->namespace . $class_name;
    $file_class_name = $this->formatClassname( $class_name ) . ".php";
    $new_file = $this->copyFile( $new_folder, $file_class_name, $this->class_template );
    if ( $new_file ) {
      $this->makeDefaultClass( $new_file, $class_name );
    }
    return $new_file;
  }

  public function createExtendClass( $class_name){
    $class_name = $this->namespace . $class_name;
    $file_class_name = $this->formatClassname( $class_name ) . ".php";
    $new_file = $this->copyFile( "extend", $file_class_name, $this->extend_template );
  }

  public function copyFile( $folder, $file_name, $template ) {
    var_dump($folder);
    if ( $folder == "" ) {
      $folder = $this->getModulePath();
    }else {
      $folder = $this->getModulePath() . $folder . "/";
    }
    // Copy the Template File to Module-Location
var_dump($folder);
    var_dump($file_name);
    if ( $this->createOrCheckFolder( $folder ) ) {
      $new_file = $folder . $file_name;
      if ( copy( getOxidGenBasePath() . $template, $new_file ) ) {
        return $new_file;
      }else {
        $this->printError( "The folder {$folder} could not be created, please check Access-Rights." );
        return false;
      }
    }else {
      $this->printError( "The folder {$folder} could not be created, please check Access-Rights." );
      return false;
    }
  }

  public function createOrCheckFolder( $folder ) {
    if ( ! is_dir( $folder ) ) {
      if ( mkdir( $folder, $this->folder_mode, true ) ) {
        return true;
      }else {
        return false;
      }
    }else {
      return true;
    }
  }

  public function makeDefaultClass( $file, $class_name ) {
    $content = file_get_contents( $file );
    $replacements = array( "classname" => $class_name );
    $content = $this->replaceContentTag( $content, $replacements );
    file_put_contents( $file, $content );
  }
  public function makeControllerClass( $file, $class_name ) {
    $content = file_get_contents( $file );
    $replacements = array(
      "classname" => $class_name,
      "templatename" => $class_name
      );
    $content = $this->replaceContentTag( $content, $replacements );
    file_put_contents( $file, $content );
  }

  public function makeLibClass( $file, $class_name ) {
    return $this->makeDefaultClass( $file, $class_name );
  }

  public function makeModelClass( $file, $class_name ) {
    $content = file_get_contents( $file );
    $replacements = array(
      "classname" => $class_name,
      "tablename" => strtolower( $class_name )
    );
    $content = $this->replaceContentTag( $content, $replacements );
    file_put_contents( $file, $content );
  }

  private function replaceContentTag( $content, $replacementArray ) {
    $replacementArray = array_merge( $this->defaultReplacementArray, $replacementArray );
    // build replacement array
    $search = array();
    $replace = array();
    foreach ( $replacementArray as $key => $value ) {
      $search[] = "{{" . $key . "}}";
      $replace[] = $value;
    }
    return str_replace( $search, $replace, $content );
  }

  private function formatClassname( $class_name = "" ) {
    return strtolower( $class_name );
  }
}
