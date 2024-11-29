
// Utility: Position and show a menu
function positionAndShowMenu(menu, event) {
  closeAllContainers();
  const menuRect = menu[0].getBoundingClientRect();
  const viewportWidth = $(window).width();
  const viewportHeight = $(window).height();

  let top = event.clientY;
  let left = event.clientX;

  // Adjust for viewport constraints
  if (left + menuRect.width > viewportWidth) left -= menuRect.width;
  if (top + menuRect.height > viewportHeight) top -= menuRect.height;

  // Ensure positive values
  top = Math.max(0, top);
  left = Math.max(0, left);

  menu.css({ top: `${top}px`, left: `${left}px`, visibility: 'visible' }).show();
}

// Utility: Close all containers except the specified one
function closeAllContainers(except = null) {
  $(".context-menu").not(except).hide();
  // $(".fimanagertoolpanel").addClass("disabledicon");
}

$(document).ready(function () {
  const appContextMenu = $("#app-contextmenu");
  const dashboardContextMenu = $("#context-menu");

  // Right Click: Show the appropriate context menu
  $(document).on("contextmenu", function (event) {
    event.preventDefault();
    const target = $(event.target);

    if (target.closest(allAppListClass+" .app").length) {
      handleAppRightClick(target, event, appContextMenu);
    } else if (target.closest(allAppListClass).length) {
      handleDashboardRightClick(event, dashboardContextMenu);
    } else {
      closeAllContainers();
    }
  });

  // Left Click: Close all containers
  $(document).on("click", function (event) {
    if (!$(event.target).closest(".context-menu, "+allAppListClass).length) {
      closeAllContainers();
    }

    

    // if($(allAppListClass+".selectedfile").length === 0){
    //   appContextMenu.addClass('hidden');
    // }
  });

  // Submenu hover logic
  $(document).on("mouseenter", ".context-menu li", function () {
    const submenu = $(this).find(".submenu");
    if (submenu.length) handleSubmenuPosition(submenu, $(this));
  });

  $(document).on("mouseleave", ".context-menu li", function () {
    const submenu = $(this).find(".submenu");
    submenu.hide();
  });

  // App click logic
  $(document).on("click", allAppListClass+ " .app-tools i", function (event) {
    handleAppClick($(this), event, appContextMenu);
  });

  // Checkbox logic
  setupCheckboxLogic();
});


function handleAppRightClick(target, event, appContextMenu) {
  const app = target.closest(allAppListClass+" .app");
  const filetype = app.find(".openiframe").data("filetype");
  $(allAppListClass+" .app").removeClass("desktopapp-clicked selectedfile");
  app.find(".openiframe").addClass("selectedfile");
  console.log($(".filemamagertab .fimanagertoolpanel"));

  if (filetype == "folder" || filetype == "file") {
    $(".filemamagertab .fimanagertoolpanel").removeClass("disabledicon");
  } else {
    $(".filemamagertab .fimanagertoolpanel").addClass("disabledicon");
  }
  $(".filemamagertab .enableonlypaste").addClass("disabledicon");

  contextMenuList(app.data("option"), appContextMenu);
  positionAndShowMenu(appContextMenu, event);
}

function handleDashboardRightClick(event, dashboardContextMenu) {
    const app = $(allAppListClass+" .app");

  $(allAppListClass+" .app").removeClass("desktopapp-clicked selectedfile");
  const filetype = app.find(".openiframe").data("filetype");
  $(".filemamagertab .fimanagertoolpanel").addClass("disabledicon");
  $(".filemamagertab .enableonlypaste").removeClass("disabledicon");
  app.find(".openiframe").addClass("selectedfile");
  contextMenuList("rightclick", dashboardContextMenu);
  positionAndShowMenu(dashboardContextMenu, event);
}

function handleAppClick(appTool, event, appContextMenu) {
  event.stopPropagation();
  const app = appTool.closest(".app");
  const filetype = app.find(".openiframe").data("filetype");

  $(allAppListClass+" .app").removeClass("desktopapp-clicked selectedfile");
  app.addClass("desktopapp-clicked");
  app.find(".openiframe").addClass("selectedfile");
  if (filetype == "folder" || filetype == "file") {
    $(".filemamagertab .fimanagertoolpanel").removeClass("disabledicon");
  } else {
    $(".filemamagertab .fimanagertoolpanel").addClass("disabledicon");
  }
  $(".filemamagertab .enableonlypaste").addClass("disabledicon");

  contextMenuList(app.data("option"), appContextMenu);
  positionAndShowMenu(appContextMenu, event);
}

function handleSubmenuPosition(submenu, parent) {
  submenu.show();

  const submenuRect = submenu[0].getBoundingClientRect();
  const screenWidth = $(window).width();
  const screenHeight = $(window).height();

  // Adjust submenu position for overflow
  if (submenuRect.right > screenWidth) submenu.css("left", `-${submenuRect.width}px`);
  if (submenuRect.bottom > screenHeight) submenu.css("top", `-${submenuRect.height - parent.outerHeight()}px`);
}

function setupCheckboxLogic() {
  const container = $(allAppListClass);

  container.on("click", ".app", function (event) {
    event.stopPropagation();
    const checkbox = $(this).find('input[type="checkbox"]');
    checkbox.prop("checked", !checkbox.prop("checked"));
  });

  container.on("click", 'input[type="checkbox"]', function (event) {
    event.stopPropagation();
    
  });
}
/// end context menu
///ajax function 
function contextMenuList(type, menu) {
  $.ajax({
    url: contextmenu,
    method: "GET",
    data: { type },
    success: function (response) {
      menu.html(response.html).show();
    },
    error: function (xhr) {
      console.error("Error loading context menu:", xhr.responseText);
    }
  });
}