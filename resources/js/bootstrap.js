import axios from 'axios';
import Swal from 'sweetalert2';
import jQuery from 'jquery';
window.axios = axios;
window.Swal = Swal;
window.$ = window.jQuery = jQuery;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
