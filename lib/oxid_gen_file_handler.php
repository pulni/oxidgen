<?php

class OxidGenFileHandler extends CLI {

  protected $vendor = "";
  protected $namespace = "";
  protected $id = "";

  protected $class_template = "templates/class_template.php";
  protected $model_template = "templates/model_template.php";
  protected $controller_template = "templates/controller_template.php";
  protected $extend_template = "templates/extend_template.php";
  protected $template_template = "templates/template_template.tpl";
  protected $template_block_template = "templates/template_block_template.tpl";
  protected $metadata_template = "templates/metadata_template.php";

  protected $folder_mode = 0777;
  protected $file_mode = 0777;

  protected $controller_suffix = "_controller";
  protected $model_suffix = "";
  protected $lib_suffix = "_lib";
  protected $template_suffix = "";

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


  public function getBasePath( $fullpath = true ) {

    if ( $fullpath ) {
      return getShopBasePath() . "modules/";
    }else {
      return "modules/";
    }
  }

  public function getModulePath( $fullpath = true ) {
    if ( $this->vendor != "" ) {
      $path = $this->vendor . "/" . $this->id;
    }else {
      $path = $this->id;
    }
    return $path . "/";
  }

  public function copyThumbnail( $thumbnail_file = "", $module_path = "" ) {

    if ( $thumbnail_file == "default.png" ) {

    }
  }

  public function createControllerClass( $folder, $class_name ) {
    $class_name = $this->namespace . $this->id . $class_name . $this->controller_suffix;
    $file_class_name = $this->formatClassname( $class_name ) . ".php";
    $new_file = $this->copyFile( $folder, $file_class_name, $this->controller_template );
    if ( $new_file ) {
      $this->makeControllerClass( $this->getBasePath() . $new_file, $class_name );
    }
    return $new_file;
  }

  public function createModelClass( $folder, $class_name ) {
    $class_name = $this->namespace . $this->id . $class_name . $this->model_suffix;
    $file_class_name = $this->formatClassname( $class_name ) . ".php";
    $new_file = $this->copyFile( $folder, $file_class_name, $this->model_template );
    if ( $new_file ) {
      $this->makeModelClass( $this->getBasePath() . $new_file, $class_name );
    }
    return $new_file;
  }

  public function createLibClass( $folder, $class_name ) {
    $class_name = $this->namespace . $this->id . $class_name . $this->lib_suffix;
    $file_class_name = $this->formatClassname( $class_name ) . ".php";
    $new_file = $this->copyFile( $folder, $file_class_name, $this->class_template );
    if ( $new_file ) {
      $this->makeLibClass( $this->getBasePath() . $new_file, $class_name );
    }
    return $new_file;
  }

  public function createClass( $folder, $class_name ) {
    $class_name = $this->namespace . $this->id . $class_name;
    $file_class_name = $this->formatClassname( $class_name ) . ".php";
    $new_file = $this->copyFile( $new_folder, $file_class_name, $this->class_template );
    if ( $new_file ) {
      $this->makeDefaultClass( $this->getBasePath() . $new_file, $class_name );
    }
    return $new_file;
  }

  public function createExtendClass( $class_name ) {
    $class_name = $this->namespace . $this->id . $class_name;
    $file_class_name = $this->formatClassname( $class_name ) . ".php";
    $new_file = $this->copyFile( "extend", $file_class_name, $this->extend_template );
    if ( $new_file ) {
      $this->makeExtendClass( $this->getBasePath() . $new_file, $class_name );
    }
    return $new_file;
  }

  public function createTemplate( $template_name, $template_type ) {
    $file_template_name = $this->formatClassname( $template_name ) . ".tpl";
    if ( $template_type == "admin" ) {
      $template_folder = "admin/out/tpl";
      $template_template = $this->template_template;
    }elseif ( $template_type == "block" ) {
      $template_folder = "out/blocks";
      $template_template = $this->template_block_template;

    }else {
      $template_folder = "out/tpl";
      $template_template = $this->template_template;
    }
    $new_template = $this->copyFile( $template_folder, $file_template_name, $template_template );
    if ( $new_template ) {
      return $template_folder ."/". $file_template_name;
    }else {
      return false;
    }

  }

  public function copyFile( $folder, $file_name, $template ) {
    if ( $folder == "" ) {
      $folder = $this->getModulePath();
    }else {
      $folder = $this->getModulePath() . $folder . "/";
    }

    // Copy the Template File to Module-Location
    if ( $this->createOrCheckFolder( $this->getBasePath() . $folder ) ) {
      $new_file = $folder . $file_name;
      $full_file_path = $this->getBasePath() . $new_file;
      if ( $this->copyOrCheckFile( $template, $full_file_path ) ) {
        if ( chmod( $full_file_path, $this->file_mode ) ) {
          return $new_file;
        }else {
          $this->printError( "The Access-Right ". $this->file_mode ." could not be set for File: " . $full_file_path );
        }
      }else {
        $this->printError( "The File " . $this->getBasePath() . $new_file . " could not be created. The File already exists." );
        return false;
      }
    }else {
      $this->printError( "The folder " . $this->getBasePath() . $folder . " could not be created, please check Access-Rights." );
      return false;
    }
  }

  public function copyOrCheckFile( $template, $new_file ) {
    if ( ! is_file( $new_file ) ) {
      if ( copy( $template, $new_file ) ) {
        return true;
      }else {
        return false;
      }
    }else {
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

  public function makeExtendClass( $file, $class_name ) {
    $content = file_get_contents( $file );
    $replacements = array(
      "classname" => $class_name
    );
    $content = $this->replaceContentTag( $content, $replacements );
    file_put_contents( $file, $content );
  }

  public function className( $class_name, $type ) {
    switch ( $type ) {
    case 'controller':
      $suffix = $this->controller_suffix;
      break;

    case 'model':
      $suffix = $this->model_suffix;
      break;

    case 'lib':
      $suffix = $this->lib_suffix;
      break;

    default:
      $suffix = "";
      break;
    }
    return $this->namespace . $this->id . $class_name . $suffix;
  }

  public function templateName( $template_name ) {
    return $this->namespace . $this->id . $template_name . $this->template_suffix;
  }

  public function writeMetaDataFile( $metaDataArray ) {
    $new_file = $this->copyFile( "", "metadata.php", $this->metadata_template );
    $file = $this->getBasePath() . $new_file;
    $content = file_get_contents( $file );
    $replacements = array(
      "aModuleArray" => $this->printArrayAsPhpCode( $metaDataArray, true )
    );
    $content = $this->replaceContentTag( $content, $replacements );
    file_put_contents( $file,  $content );
  }

  /**
   * Print an array (recursive) as PHP code (can be pasted into a php file and it will work).
   *
   * @param array   $array
   * @param boolean $return (whether to return or print the output)
   * @return string|boolean (string if $return is true, true otherwise)
   */
  public function printArrayAsPhpCode( $array, $return = false, $level = 0 ) {
    if ( count( $array ) == 0 ) {
      if ( !$return ) {
        print "array()";
        return true;
      } else {
        return "array()";
      }
    }
    $spaces = 4;
    $indent = str_repeat( " ", $spaces * ( $level + 1 ) );
    $indent_outer = str_repeat( " ", $spaces * $level );
    $string = $indent_outer . "array(";
    if ( array_values( $array ) === $array ) {
      $no_keys = true;
      foreach ( $array as $value ) {
        if ( is_int( $value ) ) {
          $string .= $indent . "$value, ";
        } elseif ( is_array( $value ) ) {
          $string .= $indent . $this->printArrayAsPhpCode( $value, true, $level + 1 ) . ",\n";
        } elseif ( is_string( $value ) ) {
          $string .= $indent . "$value', ";
        } else {
          trigger_error( "Unsupported type of \$value, in index $key." );
        }
      }
    } else {
      $string .="\n";
      foreach ( $array as $key => $value ) {
        $no_keys = false;
        if ( is_int( $value ) ) {
          $string .= $indent . "'$key' => $value,\n";
        } elseif ( is_array( $value ) ) {
          $string .= $indent . "'$key' => " . $this->printArrayAsPhpCode( $value, true, $level + 1 ) . ",\n";
        } elseif ( is_string( $value ) ) {
          $string .= $indent . "'$key' => '$value',\n";
        } else {
          trigger_error( "Unsupported type of \$value, in index $key." );
        }
      }
    }
    $string = substr( $string, 0, strlen( $string ) - 2 ); // Remove last comma.
    if ( !$no_keys ) {
      $string .= "\n";
    }
    $string .= $indent_outer . ")";
    if ( !$return ) {
      print $string;
      return true;
    } else {
      return $string;
    }
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
