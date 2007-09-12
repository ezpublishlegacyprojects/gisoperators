<?php
//
// Definition of GISOperators Methods
//
// GISOperators Methods
//
// Created on: <08-Sep-2007 00:08:00 Norman Leutner>
// Last Updated: <08-Sep-2007 00:14:58 Norman Leutner>
// Version: 0.0.1
//
// Copyright (C) 2001-2007 all2e GmbH. All rights reserved.
//
// This source file is part of an extension for the eZ publish (tm)
// Open Source Content Management System.
//
// This file may be distributed and/or modified under the terms of the
// "GNU General Public License" version 2 (or greater) as published by
// the Free Software Foundation and appearing in the file LICENSE
// included in the packaging of this file.
//
// This file is provided AS IS with NO WARRANTY OF ANY KIND, INCLUDING
// THE WARRANTY OF DESIGN, MERCHANTABILITY AND FITNESS FOR A PARTICULAR
// PURPOSE.
//
// The "GNU General Public License" (GPL) is available at
// http://www.gnu.org/copyleft/gpl.html
//
// Contact info@all2e.com if any conditions
// of this licencing isn't clear to you.
//

include_once( "lib/ezutils/classes/ezdebug.php" );
include_once( "lib/ezxml/classes/ezxml.php" );
include_once( "extension/gisoperators/classes/googlegeocoder.php" );

class GISOperators
{
    /*!
     Constructor
    */
    function GISOperators()
    {
        $this->Operators = array( 'gisrange','gisposition');
    }

    /*!
     Returns the operators in this class.
    */
    function &operatorList()
    {
        return $this->Operators;
    }

    /*!
     \return true to tell the template engine that the parameter list
    exists per operator type, this is needed for operator classes
    that have multiple operators.
    */
    function namedParameterPerOperator()
    {
        return true;
    }

    /*!
     The first operator has two parameters.
     See eZTemplateOperator::namedParameterList()
    */
    function namedParameterList()
    {
        return array( 'gisrange' => array( 'searchString' => array( 'type' => 'string',
                                                                                   'required' => true ),
                                                     'range' =>       array( 'type' => 'int',
                                                                                   'required' => true ) ), 
                          'gisposition' => array( 'searchString' => array( 'type' => 'string',
                                                                                   'required' => true ) )
                        );    
    }

    /*!
     Executes the needed operator(s).
     Checks operator names, and calls the appropriate functions.
    */
    function modify( &$tpl, &$operatorName, &$operatorParameters, &$rootNamespace,
                     &$currentNamespace, &$operatorValue, &$namedParameters )
    {
        switch ( $operatorName )
        {
            case 'gisrange':
            {
                        
                $db =& eZDB::instance();

                $geocode = new GoogleGeoCoder();
                $geocode->request($namedParameters['searchString']);
                
                //var_dump($geocode);
                
                if ($geocode->statuscode == "200")
                {
                    // Bogenmaße des Standortes
                    $phi = $geocode->phi; // Phi von Longitude 
                    $theta = $geocode->theta; // Theta von Latitude                                         
                    $radius = 6367.46; // Mittelwert zwichen Äquator und Polradius
                    $range = $namedParameters['range'];

                    // Query berechnet Orthodrome Entfernung zwischen gegebenen Punkt und aller in der Datenbank vorhandenen Geodaten
                    // Abweichung beträgt etwa 50km auf 8000km (Entfernung Berlin -> Tokio), da die Formel von der Erde als runde Kugel ausgeht
                    // und nicht das GRS80-Ellipsoid Modell zugrunde legt.
                    //$query=SELECT street, zip, city, state, country, ".$radius."*ACOS(cos(RADIANS(latitude))*cos(".$theta.")*(sin(RADIANS(longitude))*sin(".$phi.")+cos(RADIANS(longitude))*cos(".$phi."))+sin(RADIANS(latitude))*sin(".$theta.")) AS Distance FROM ezgis_position WHERE ".$radius."*ACOS(cos(RADIANS(latitude))*cos(".$theta.")*(sin(RADIANS(longitude))*sin(".$phi.")+cos(RADIANS(longitude))*cos(".$phi."))+sin(RADIANS(latitude))*sin(".$theta.")) <= ".$range." ORDER BY Distance";
                    $query="SELECT                                     
                                    ezcontentobject_tree.node_id,
                                    ezcontentobject.id, 
                                    ezgis_position.street, 
                                    ezgis_position.zip, 
                                    ezgis_position.city, 
                                    ezgis_position.state, 
                                    ezgis_position.country,
                                    ".$radius."*ACOS(cos(RADIANS(latitude))*cos(".$theta.")*(sin(RADIANS(longitude))*sin(".$phi.")+cos(RADIANS(longitude))*cos(".$phi."))+sin(RADIANS(latitude))*sin(".$theta.")) AS Distance

                                FROM 
                                    ezcontentobject_tree,
                                    ezcontentobject, 
                                    ezcontentobject_attribute, 
                                    ezgis_position 

                                WHERE 
                                    ".$radius."*ACOS(cos(RADIANS(latitude))*cos(".$theta.")*(sin(RADIANS(longitude))*sin(".$phi.")+cos(RADIANS(longitude))*cos(".$phi."))+sin(RADIANS(latitude))*sin(".$theta.")) <= ".$range ."
                                    AND ezcontentobject_tree.contentobject_id = ezcontentobject.id
                                    AND ezcontentobject.id = ezcontentobject_attribute.contentobject_id
                                    AND ezcontentobject.current_version = ezcontentobject_attribute.version
                                    AND ezcontentobject_attribute.id = ezgis_position.contentobject_attribute_id
                                    AND ezcontentobject.current_version = ezgis_position.contentobject_attribute_version
                                ORDER BY Distance";

                    $ResultArray =& $db->arrayQuery( $query );
                    $operatorValue = $ResultArray;
                }
                else 
                {
                    eZDebug::writeError( "gisrange: Google returned statuscode: ". $geocode->statuscode);
                    $operatorValue = false;
                }
                return;
                
            } break;   
                      
                      
            case 'gisposition':
            {

                $geocode = new GoogleGeoCoder();
                $geocode->request($namedParameters['searchString']);
                
                //var_dump($geocode);
                
                if ($geocode->statuscode == "200")
                {                
                    $operatorValue['longitude'] = $geocode->longitude;
                    $operatorValue['latitude'] = $geocode->latitude;
                }
                else 
                {
                    eZDebug::writeError( "gisposition: Google returned statuscode: ". $geocode->statuscode);
                    $operatorValue = false;
                }

            } break;    
            
            
        }
    }

    /// \privatesection
    var $Operators;
}

?>

