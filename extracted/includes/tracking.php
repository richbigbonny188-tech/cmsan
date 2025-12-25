<?php
/* --------------------------------------------------------------
   tracking.inc.php 2021-05-14
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------

   based on:
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: tracking.php 1151 2005-08-12 09:19:33Z gwinger $)

   Released under the GNU General Public License
   -----------------------------------------------------------------------------------------
   Third Party contribution:
   Some ideas and code from TrackPro v1.0 Web Traffic Analyzer 
   Copyright (C) 2004 Curve2 Design www.curve2.com

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

/**
 * @deprecated since GX 4.5, use \Gambio\GX\Application::trackUser() instead
 */

use Gambio\GX\Application;

Application::trackUser();
