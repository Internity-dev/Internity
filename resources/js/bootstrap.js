import _ from "lodash";
window._ = _;

import axios from "axios";
window.axios = axios;

window.axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";

import * as bootstrap from "bootstrap";
window.bootstrap = bootstrap;

import Trix from "trix";

import "../css/app.scss";
import "iconify-icon";
