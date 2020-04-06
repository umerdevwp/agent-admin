import { toAbsoluteUrl } from "../utils/utils";

export const initLayoutConfig = {
  "demo": "AgentAdmin",
  "self": {
    "layout": "fluid",
    "logo": {
      "dark": "/media/logos/logo-light.png",
      "light": "/media/logos/logo-dark.png",
      "brand": "/media/logos/logo-light.png",
      "green": "/media/logos/logo-light.png"
    }
  },
  "loader": {
    "enabled": true,
    "type": "spinner-logo",
    "logo": "/media/logos/logo-mini-md.png",
    "message": "Please wait..."
  },
  "colors": {
    "state": {
      "brand": "#5d78ff",
      "dark": "#282a3c",
      "light": "#ffffff",
      "primary": "#5867dd",
      "success": "#34bfa3",
      "info": "#36a3f7",
      "warning": "#ffb822",
      "danger": "#fd3995"
    },
    "base": {
      "label": [
        "#c5cbe3",
        "#a1a8c3",
        "#3d4465",
        "#3e4466"
      ],
      "shape": [
        "#f0f3ff",
        "#d9dffa",
        "#afb4d4",
        "#646c9a"
      ]
    }
  },
  "toolbar": {
    "display": true
  },
  "header": {
    "self": {
      "width": "fluid",
      "skin": "dark",
      "fixed": {
        "desktop": true,
        "mobile": true
      }
    },
    "menu": {
      "self": {
        "display": true,
        "layout": "default",
        "root-arrow": false,
        "icon-style": "duotone"
      },
      "desktop": {
        "arrow": true,
        "toggle": "click",
        "submenu": {
          "skin": "dark",
          "arrow": true
        }
      },
      "mobile": {
        "submenu": {
          "skin": "dark",
          "accordion": true
        }
      }
    }
  },
  "subheader": {
    "display": false,
    "displayDesc": false,
    "displayDaterangepicker": true,
    "layout": "subheader-v1",
    "fixed": true,
    "width": "fluid",
    "clear": false,
    "style": "solid"
  },
  "content": {
    "width": "fluid"
  },
  "brand": {
    "self": {
      "skin": "dark"
    }
  },
  "aside": {
    "self": {
      "skin": "dark",
      "display": true,
      "fixed": true,
      "minimize": {
        "toggle": true,
        "default": false
      }
    },
    "footer": {
      "self": {
        "display": true
      }
    },
    "menu": {
      "dropdown": "false",
      "scroll": false,
      "icon-style": "duotone",
      "submenu": {
        "accordion": true,
        "dropdown": {
          "arrow": true,
          "hover-timeout": 100
        }
      }
    }
  },
  "footer": {
    "self": {
      "width": "fluid",
      "fixed": false
    }
  }
};

let LayoutConfig = JSON.parse(JSON.stringify(initLayoutConfig)); // deep object copy
export default LayoutConfig;
