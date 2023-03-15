/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// Importa personalización para Boostrap
import './styles/app.scss';

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.css';

// start the Stimulus application
import './bootstrap';

// Importa Boostrap a nivel de aplicación
import 'bootstrap';

// Se agrega jQuery
const $ = require('jquery');

// create global $ and jQuery variables
global.$ = global.jQuery = $;

// Agrega DataTables
import DataTable from 'datatables.net-bs5';
import 'datatables.net-bs5';


import 'datatables.net-buttons-bs5';                           
import 'datatables.net-buttons/js/buttons.html5.js';
import 'datatables.net-buttons/js/buttons.print.js';  

import * as pdfMake from 'pdfmake/build/pdfmake.js';
import * as pdfFonts from 'pdfmake/build/vfs_fonts.js';
pdfMake.vfs = pdfFonts.pdfMake.vfs;

var dt = require( 'datatables.net-bs5' );

// Agrego FOSJsRouting
const routes = require('./js_routes.json');
import Routing from '../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min.js';

Routing.setRoutingData(routes);