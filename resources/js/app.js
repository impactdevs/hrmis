import "./bootstrap";

// import Toastify from 'toastify-js'
// import "toastify-js/src/toastify.css"

// window.Toastify = Toastify;

window.isAdminOrSecretary = window.Laravel?.user?.isAdminOrSecretary ?? false;
