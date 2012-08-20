#!/usr/bin/php
<?php

include "lib/cli.php";
include 'lib/oxid_gen_file_handler.php';

/**
 * Returns shop base path.
 *
 * @return string
 */
function getShopBasePath() {
    return dirname( __FILE__ ).'/../OXID_ESHOP_CE_4/';
}
function getOxidGenBasePath() {
    return dirname( __FILE__ ).'/';
}

class OxidGen extends CLI
{
    protected $sMetadataVersion = "1.0";
    protected $vendor = "";
    protected $namespace = "mude";
    protected $id = "foobar";
    protected $title = "module";
    protected $description = "";
    protected $lang = "de";
    protected $thumbnail = "default.png";
    protected $version = "0.0.1";
    protected $author = "";
    protected $url = "example.com";
    protected $email = "mail@example.com";
    protected $extend = array();
    protected $files = array();
    protected $blocks = array();
    protected $settings = array();
    protected $templates = array();

    protected $aModules = array();

    protected $requiredOptions = array( "namespace", "id", "title" );

    public function __construct( $initialize = true ) {
        $this->fileHandler = new OxidGenFileHandler();
        parent::__construct( $initialize );
    }

    public function runExtension() {

        $extensionName = $this->getInput( "bitte den namen der Extension angeben" );
        $this->printInfo( $this->colorText( "Extension: {$extensionName}", self::MAGENTA ) );

        $this->requiredOptions = array( "namespace", "id", "title", "description",
            "lang", "thumbnail", "version", "author", "url", "email", "extend",
            "files", "blocks", "settings", "templates" );

        foreach ( $this->requiredOptions as $key => $option ) {
            $method = "option". ucfirst( strtolower( $option ) );
            if ( method_exists( $this, $method ) ) {
                call_user_func( array( $this, $method ) );
            }
        }

        $this->printSeperator( "_" );
        $this->buildExtensionArray();
        $this->writeMetaDataFile();
    }

    protected function optionMetadataversion( $metadataversion_from_option = "" ) {
        if ( $metadataversion_from_option != "" ) {
            $this->sMetadataVersion = $metadataversion_from_option;
        }
    }

    protected function optionVersion( $version_from_option = "" ) {
        if ( $version_from_option != "" ) {
            $this->version = $version_from_option;
        }
    }

    protected function optionNamespace( $namespace_from_option = "" ) {
        if ( $namespace_from_option != "" ) {
            $this->version = $version_from_option;
        }else {
            $this->namespace = $this->getInput( "Please enter the Namespace for the Extension:" );
        }
        if ( $this->namespace == "" ) {
            $this->optionNamespace();
        }
    }

    protected function optionId( $id_from_option = "" ) {
        if ( $id_from_option != "" ) {
            $this->id = $id_from_option;
        }else {
            $this->id = $this->getInput( "Please enter the ID for this Extension:" );
        }
        if ( $this->id == "" ) {
            $this->optionId();
        }
    }

    protected function optionTitle( $title_from_option = "" ) {
        if ( $title_from_option != "" ) {
            $this->title = $title_from_option;
        }else {
            $this->title = $this->getInput( "Please enter the Title for this Extension:" );
        }
        if ( $this->title == "" ) {
            $this->optionTitle();
        }
    }

    protected function optionDescription( $description_from_option = "" ) {
        if ( $description_from_option != "" ) {
            $this->description = $description_from_option;
        }
    }

    protected function optionLang( $lang_from_option = "" ) {
        if ( $lang_from_option != "" ) {
            $this->lang = $lang_from_option;
        }
    }

    protected function optionThumbnail( $thumbnail_from_option = "" ) {
        if ( $thumbnail_from_option != "" ) {
            $this->thumbnail = $thumbnail_from_option;
        }
    }

    protected function optionAuthor( $author_from_option = "" ) {
        if ( $author_from_option != "" ) {
            $this->author = $author_from_option;
        }
    }

    protected function optionUrl( $url_from_option = "" ) {
        if ( $url_from_option != "" ) {
            $this->url = $url_from_option;
        }
    }

    protected function optionEmail( $email_from_option = "" ) {
        if ( $email_from_option != "" ) {
            $this->email = $email_from_option;
        }
    }


    protected function optionExtend( $extend_from_option = "" ) {
        if ( $extend_from_option != "" ) {
            $extend_string = $lang_from_option;
        }else {
            $extend_string = $this->getInput( "Please enter the Oxid-Classes you want to extend, seperated by a Semicolon:" );
        }
        if ( $extend_string != "" ) {
            $this->extend = explode( ";" , str_replace( " ", "", $extend_string ) );
        }
    }

    protected function optionFiles( $files_from_option = "" ) {
        if ( $files_from_option != "" ) {
            $files_string = $files_from_option;
        }else {
            $this->printInfo( "Please specify the new Components of your Extension:", true );
            $this->printSeperator( "-" );
            $this->printSeperator();
            $this->printInfo( "Allways use the following format: Type:ClassName. To add more, seperate the Entrys by Semicolon.", true );
            $this->printSeperator();
            $this->printInfo( "The Type can be one of the following Options:", true );
            $this->printInfo( "controller - ViewController also know as the 'cl' parameter. We will automaticly create the Template File.", true );
            $this->printInfo( "model - Oxid stores these type of Files in the Core Folder.", true );
            $this->printInfo( "lib - Classes which will be created in the lib Folder.", true );
            $this->printInfo( "any - You can simply specify a Foldername (except controller, model or lib) and we create the class in this folder.", true );
            $this->printSeperator( "-" );
            $files_string = $this->getInput( "Remember: Type:ClassName >>>" ) . ";";
        }

        if ( $files_string != "" ) {
            $files_array = explode( ";" , str_replace( " ", "", $files_string ) );
            foreach ( $files_array as $file_string ) {
                if ( $file_string != "" ) {
                    $single_file_array = explode( ":", $file_string );
                    $this->files[$single_file_array[0]] = $single_file_array[1];
                }
            }
        }
    }

    protected function optionTemplates( $templates_from_option = "" ) {
        if ( $templates_from_option != "" ) {
            $templates_string = $templates_from_option;
        }else {
            $this->printInfo( "Add a new Template to the Extension:", true );
            $this->printSeperator( "-" );
            $this->printSeperator();
            $this->printInfo( "Specify if the Template should be an Admin Template. To add more, seperate the Entrys by Semicolon.", true );
            $this->printSeperator();
            $this->printInfo( "Set the ThemeType to 'admin' to add an Admin Template.", true );
            $this->printInfo( "Set the ThemeType to 'theme' to add a normal Template.", true );
            $this->printSeperator();
            $this->printSeperator( "-" );
            $templates_string = $this->getInput( "Remember: ThemeType:<TemplateName> >>>" ) . ";";
        }
        if ( $templates_string != "" ) {
            $templates_array = explode( ";" , str_replace( " ", "", $templates_string ) );
            foreach ( $templates_array as $template_string ) {
                if ( $template_string != "" ) {
                    $single_template_array = explode( ":", $template_string );
                    $this->templates[$single_template_array[0]] = $single_template_array[1];
                }
            }
        }
    }


    protected function optionBlocks( $blocks_from_option = "" ) {
        $default_order = array( 'template', 'block' );
        if ( $blocks_from_option != "" ) {
            $blocks_string = $blocks_from_option;
        }else {
            $this->printInfo( "Add a new Block to the Extension:", true );
            $this->printSeperator( "-" );
            $this->printSeperator();
            $this->printInfo( "Use the following Format to specify a block. To add more, seperate the Entrys by Semicolon.", true );
            $this->printSeperator();
            $this->printInfo( "template: - The original Template where the block is located.", true );
            $this->printInfo( "block: - The name of the Block yyou want to extent/override.", true );
            $this->printInfo( "Concat both with a comma.", true );
            $this->printSeperator();
            $this->printSeperator( "-" );
            $blocks_string = $this->getInput( "Remember: template:<TemplateName>, block:<BlockName> >>>" ) . ";";
        }
        if ( $blocks_string != "" ) {
            $blocks_array = explode( ";" , str_replace( " ", "", $blocks_string ) );
            foreach ( $blocks_array as $block_string ) {
                if ( $block_string != "" ) {
                    $single_block_array = explode( ",", $block_string );

                    $sorted_single_block_array = array();
                    //nice ordering
                    foreach ( $default_order as $key ) {
                        foreach ( $single_block_array as $single_block_string ) {
                            if ( strpos( strtolower( $single_block_string ), $key .':' ) !== false ) {
                                if ( $single_block_string != "" ) {
                                    $val_array = explode( ":", $single_block_string );
                                    $sorted_single_block_array[$key] = $val_array[1];
                                }
                            }
                        }
                    }
                    $this->blocks[] = $sorted_single_block_array;

                }
            }
        }
    }

    protected function buildExtensionArray() {

        $this->fileHandler->setId( $this->id );
        $this->fileHandler->setNamespace( $this->namespace );
        $this->fileHandler->setVendor( $this->vendor );

        if ( empty( $this->aModules ) ) {

            $this->aModules["sMetadataVersion"] = $this->sMetadataVersion;
            $this->aModules["id"] = $this->id;
            $this->aModules["title"] = $this->title;
            $this->aModules["version"] = $this->version;
            if ( $this->description != "" ) {
                $this->aModules["description"] = $this->description;
            }
            $this->aModules["author"] = $this->author;
            $this->aModules["url"] = $this->url;
            $this->aModules["email"] = $this->email;
            if ( $this->lang != "" ) {
                $this->aModules["lang"] = $this->lang;
            }
            if ( $this->thumbnail != "" ) {
                $this->aModules["thumbnail"] = $this->thumbnail;
            }
            $this->aModules["files"] = $this->processFiles( $this->files );
            $this->aModules["extend"] = $this->processExtend( $this->extend );
            $this->aModules["templates"] = $this->processTemplates( $this->templates );
            $this->aModules["blocks"] = $this->processBlocks($this->blocks);
        }
    }

    protected function processFiles( $files_array = array() ) {
        $processed_files = array();
        if ( ! empty( $files_array ) ) {
            ksort( $files_array );
            foreach ( $files_array as $type => $class_name ) {
                switch ( $type ) {
                case 'controller':
                    // special case because we also add an entry to the templates array
                    $this->templates[strtolower( $class_name )];
                    $processed_files[$class_name] = $this->fileHandler->createControllerClass( strtolower( $type ), strtolower( $class_name ) );
                    break;

                case 'model':
                    $processed_files[$class_name] = $this->fileHandler->createModelClass( strtolower( $type ), strtolower( $class_name ) );
                    break;

                case 'lib':
                    $processed_files[$class_name] = $this->fileHandler->createLibClass( strtolower( $type ), strtolower( $class_name ) );
                    break;

                default:
                    $processed_files[$class_name] = $this->fileHandler->createClass( strtolower( $type ), strtolower( $class_name ) );
                    break;
                }
            }
        }
        return $processed_files;
    }

    protected function processExtend( $extend_array = array() ) {
        $processed_extends = array();
        if ( ! empty( $extend_array ) ) {
            ksort( $extend_array );
            foreach ( $extend_array as $extend_class_name ) {
                $processed_extends[$extend_class_name] = $this->fileHandler->createExtendClass( $extend_class_name );
            }
        }
        return $processed_extends;
    }

    protected function processTemplates( $templates_array = array() ) {
        $processed_templates = array();
        if ( ! empty( $templates_array ) ) {
            ksort( $templates_array );
            foreach ( $templates_array as $type => $template_name ) {
                $template_name = $this->fileHandler->templateName( $template_name );
                $processed_templates[$template_name] = $this->fileHandler->createTemplate( $template_name, $type );
            }
        }
        return $processed_templates;
    }

    protected function processBlocks( $blocks_array = array() ) {
        $processed_blocks = array();
        if ( ! empty( $blocks_array ) ) {
            foreach ( $blocks_array as $single_block_array ) {
                $block_template_name = $this->fileHandler->templateName( $single_block_array['block'] );
                $single_block_array['file'] = $block_template_name;
                $this->fileHandler->createTemplate( $block_template_name, 'block' );
                $processed_blocks[] = $single_block_array;
            }
        }
        return $processed_blocks;
    }

    protected function writeMetaDataFile() {
        $this->fileHandler->writeMetaDataFile( $this->aModules );
    }
}


new OxidGen();
