import { extend } from "flarum/extend";
import app from "flarum/app";
import ImagerModal from "./components/ImagerModal";
import PermissionGrid from "flarum/components/PermissionGrid";

app.initializers.add("artuu-imager", (app) => {
  app.extensionSettings["artuu-imager"] = () => app.modal.show(new ImagerModal());

  extend(PermissionGrid.prototype, "replyItems", (items) => {
    items.add("artuu-imager", {
      icon: "far fa-user-circle",
      label: app.translator.trans("artuu-imager.admin.permissions.resized_avatar"),
      permission: "artuu-imager.resized_avatar",
    });
  });
});

// Expose compat API
import imagerCompat from "./compat";
import { compat } from "@flarum/core/admin";

Object.assign(compat, imagerCompat);
