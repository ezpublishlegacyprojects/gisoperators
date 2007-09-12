<?php
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

define('GIS_OPERATORS_FOLDER','gisoperators');

// Operator autoloading

$eZTemplateOperatorArray = array();

$eZTemplateOperatorArray[] =
  array( 'script' => eZExtension::baseDirectory() . '/' . GIS_OPERATORS_FOLDER . '/autoloads/gisoperators.php',
         'class' => 'GISOperators',
         'operator_names' => array( 'gisrange','gisposition') );
         

?>
