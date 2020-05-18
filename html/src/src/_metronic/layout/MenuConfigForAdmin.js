export default {
  header: {
    self: {},
    items: [
      {
        title: "Dashboards",
        root: true,
        alignment: "left",
        page: "dashboard",
        translate: "MENU.DASHBOARD"
      }
    ]
  },
  aside: {
    self: {},
    items: [
      {
        title: "Dashboard",
        root: true,
        icon: "flaticon2-architecture-and-city",
        page: "dashboard",
        translate: "MENU.DASHBOARD",
        bullet: "dot"
      },

      {
        title: "Add Entity",
        root: true,
        icon: "flaticon2-user",
        page: "dashboard/entity/form/add",
        bullet: "dot"
      },
      {
        title: "Contacts",
        root: true,
        icon: "flaticon2-phone",
        page: "dashboard/contacts",
        bullet: "dot"
      },
      {
        title: "Attachments",
        root: true,
        icon: "flaticon-attachment",
        page: "dashboard/attachments",
        bullet: "dot"
      },


      {
        title: "Administrator",
        root: true,
        icon: "flaticon2-user-1",
        page: "dashboard/admins",
        bullet: "dot"
      },


      {
        title: "Registered Agents",
        root: true,
        icon: "flaticon2-user-1",
        page: "dashboard/agents",
        bullet: "dot"
      },


    ]
  }
};
