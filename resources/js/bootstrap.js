import axios from "axios";
import "../sass/app.scss";
import "bootstrap";

window.axios = axios;

window.axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";
window.axios.defaults.withCredentials = true;
