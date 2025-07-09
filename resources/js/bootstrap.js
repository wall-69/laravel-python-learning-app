import axios from "axios";
import "../sass/app.scss";
import "bootstrap";

window.axios = axios;

window.axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";
