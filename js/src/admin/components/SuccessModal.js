import app from "flarum/app";
import Modal from "flarum/components/Modal";

export default class SuccessModal extends Modal {
  init() {
    setTimeout(() => {
      this.hide();
    }, 2500);
  }

  title() {
    return app.translator.trans("artuu-imager.admin.settings.success");
  }

  className() {
    return "SuccessModal Modal--small";
  }

  isDismissible() {
    return false;
  }

  content() {
    // Animation autor: https://codepen.io/jimmis/pen/rNNPdLN
    return (
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 386.46 312.19">
        <defs></defs>
        <g xmlns="http://www.w3.org/2000/svg" id="Layer_1" data-name="Layer 1">
          <circle class="cls-1 swirl1" cx="159.56" cy="23.16" r="8" />
          <circle class="cls-2 swirl2" cx="349.18" cy="164.71" r="5.25" />
          <circle class="cls-2 swirl1" cx="239.24" cy="286.61" r="5.25" />
          <circle class="cls-2 swirl2" cx="19.41" cy="159.14" r="5.25" />
          <circle class="cls-3 swirl1" cx="43.87" cy="132.16" r="5" />
          <circle class="cls-3 bounce2" cx="371.07" cy="190.3" r="5" />
          <circle class="cls-3 swirl2" cx="112.96" cy="21.16" r="5" />
          <circle class="cls-3 bounce2" cx="57.68" cy="213.26" r="5" />
          <circle class="cls-3 swirl1" cx="320.87" cy="201.26" r="7" />
          <circle class="cls-3" cx="196.4" cy="160.51" r="109.34" />
        </g>
        <g class="check" xmlns="http://www.w3.org/2000/svg" id="Layer_2" data-name="Layer 2">
          <circle class="cls-4 bounce" cx="107.96" cy="76.92" r="35.28" />
          <polyline class="cls-5 bounce" points="91.79 79.85 101.99 90.05 125.51 66.53" />
        </g>
        <g xmlns="http://www.w3.org/2000/svg" id="Layer_3" data-name="Layer 3">
          <path class="cls-6 dash" d="M258.53,259c0-34.68-27.82-62.79-62.13-62.79S134.27,224.35,134.27,259" />
          <circle class="cls-6 dash" cx="196.4" cy="147.46" r="42.84" />
          <path class="cls-6 dash2" d="M181.12,160a16.26,16.26,0,0,0,30.56-.86" />
          <circle class="cls-4" cx="180.51" cy="140.1" r="5.33" />
          <circle class="cls-4" cx="212.45" cy="140.1" r="5.33" />
        </g>
      </svg>
    );
  }
}
