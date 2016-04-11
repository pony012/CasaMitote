/**
* these are the main modules to be loaded in all the jsx files
* @author Ramón Lozano <ramon.lozano@ttr.com.mx>
* @require {@link materialize-css/sass/materialize.scss}
* @require {@link ../../sass/main.scss}
* @require module:materialize-css
* @require module:react-tap-event-plugin
* @since 0.1.0
* @version 0.1.0
*/

require('materialize-css/sass/materialize.scss');
import '../../sass/main.scss';
import 'materialize-css';

var injectTapEventPlugin = require("react-tap-event-plugin");
injectTapEventPlugin();