import "../css/app.scss";

import _ from "lodash";
window._ = _;

import axios from "axios";
window.axios = axios;

window.axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";

import * as bootstrap from "bootstrap";
window.bootstrap = bootstrap;

import Trix from "trix";

// import sweetalert
import swal from "sweetalert";
window.swal = swal;

import utils from "./utils";
window.utils = utils;

// import $ from "jquery";
// window.$ = $;

import "iconify-icon";

import Picker from "vanilla-picker";
window.Picker = Picker;

import Chart from "chart.js/auto";
window.Chart = Chart;
