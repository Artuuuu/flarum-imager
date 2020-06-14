import app from "flarum/app";
import Button from "flarum/components/Button";
import Dropdown from "flarum/components/Dropdown";
import SettingsModal from "flarum/components/SettingsModal";
import SuccessModal from "./SuccessModal";

export default class ImagerModal extends SettingsModal {
  title() {
    return app.translator.trans("artuu-imager.admin.settings.title");
  }

  className() {
    return "ImagerSettingsModal Modal--small";
  }

  form() {
    return [
      m(".Form-group", m("p", app.translator.trans("artuu-imager.admin.settings.about"))),
      m(".Form-group", [
        m("label", [
          app.translator.trans("artuu-imager.admin.settings.favicon_size", {
            input: m("input.FormControl", {
              className: "",
              bidi: this.setting("artuu-imager.favicon_size"),
              value: this.setting("artuu-imager.favicon_size"),
              placeholder: "64",
              type: "number",
              min: 64,
              max: 1024,
            }),
          }),
        ]),
      ]),

      m(".Form-group", [
        m("label", [
          app.translator.trans("artuu-imager.admin.settings.logo_size", {
            input: m("input.FormControl", {
              bidi: this.setting("artuu-imager.logo_size"),
              value: this.setting("artuu-imager.logo_size"),
              placeholder: "60",
              type: "number",
              min: 60,
              max: 540,
            }),
          }),
        ]),
      ]),

      m(".Form-group", [
        m("label", [
          app.translator.trans("artuu-imager.admin.settings.avatars_size", {
            input: m("input.FormControl", {
              bidi: this.setting("artuu-imager.avatars_size"),
              value: this.setting("artuu-imager.avatars_size"),
              placeholder: "100",
              type: "number",
              min: 100,
              max: 2048,
            }),
          }),
        ]),
      ]),
    ];
  }
  content() {
    return m(".Modal-body", [
      m(".Form", [
        this.form(),
        m(".Form-group", [
          this.submitButton(),

          Dropdown.component({
            buttonClassName: "Button",
            label: app.translator.trans("artuu-imager.admin.settings.delete_files"),
            children: [
              [
                { file: "avatars", icon: "far fa-user-circle" },
                { file: "logo", icon: "far fa-image" },
                { file: "favicon", icon: "far fa-file-image" },
                { file: "all", icon: "far fa-images" },
              ].map((child) => {
                return Button.component({
                  className: "Button",
                  children: app.translator.trans("artuu-imager.admin.settings.disposal." + child.file),
                  icon: child.icon,
                  onclick: () => this.delete(child.file),
                });
              }),
            ],
          }),
        ]),
      ]),
    ]);
  }

  delete(files) {
    app
      .request({
        url: "/api/imager",
        method: "DELETE",
        data: {
          [files]: true,
        },
      })
      .then(() => {
        this.hide();
        app.modal.show(new SuccessModal());
      });
  }
}
