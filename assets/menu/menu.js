$(document).ready(function(){

  menu_access_view();
  
  });
  
  // AJAX call to fetch branch data
  function menu_access_view() {
  
    $.ajax({
      url: menuaccessurl,
      type: "GET",
      success: handleSuccessmenu,
      error: handleErrormenu,
    });
  }
  
  // Handle successful AJAX response
  // function handleSuccessmenu(responseData) {
  //   let menuupdatesallview = "";
  //   console.log(responseData);
  
  //   $.each(responseData, function (index, user) {
  //     let routeUrl = menuRouteBase + user.route; // Construct the route dynamically
  //     menuupdatesallview += '<a href="' + routeUrl + '" class="pc-link"><span class="pc-micon" ><img src="https://draravinds.com/hms/assets/images/'+user.icon+'" style="width: 19px;" alt="Icon" class="icon"></span><span class="pc-mtext" id="'+user.active_ids+'">' + user.menu_name + '</span></a>';
  //   });
  //   $(".menuupdatesall").html(menuupdatesallview);
  // }\
  
  
  
  function handleSuccessmenu(responseData) {
    if (!responseData || responseData.length === 0) {
      console.warn("No menu data received.");
      return;
    }
  
    const currentPath = window.location.pathname;
    let menuHTML = "";
  
    $.each(responseData, function (index, menu) {
      const iconUrl = `/hms/assets/images/${menu.icon || 'default-icon.png'}`;
      let isActiveDropdown = false;
      let submenuHTML = "";
  
      console.log(menu);
      
      // Only process main_menu items
      if (menu.main_menu == 1) {
        // If dropdown, process children
        if (menu.dropdown == 1 && menu.children && menu.children.length > 0) {
          $.each(menu.children, function (i, child) {
            const childUrl = '/' + (child.route || '').replace(/\./g, '/');
            const isChildActive = currentPath === childUrl;
            //let routeUrl = menuRouteBase + user.route;
  
            if (isChildActive) {
              isActiveDropdown = true;
            }
  
            submenuHTML += `
              <li class="pc-item ${isChildActive ? 'active' : ''}">
                <a href="${menuRouteBase + childUrl}" class="pc-link">${child.menu_name}</a>
              </li>
            `;
          });
  
          menuHTML += `
            <li class="pc-item pc-hasmenu ${isActiveDropdown ? 'active open' : ''}">
              <a href="javascript:void(0);" class="pc-link">
                <span class="pc-micon">
                  <img src="${iconUrl}" style="width: 22px;" alt="Icon" class="icon">
                </span>
                <span class="pc-mtext">${menu.menu_name}</span>
              </a>
              <ul class="pc-submenu" style="${isActiveDropdown ? 'display: block;' : 'display: none;'}">
                ${submenuHTML}
              </ul>
            </li>
          `;
        } else {
          // Single level main menu item
          const routeUrl = '/' + (menu.route || '').replace(/\./g, '/');
          const isActive = currentPath === routeUrl;
  
          menuHTML += `
            <li class="pc-item ${isActive ? 'active' : ''}">
              <a href="${menuRouteBase+routeUrl}" class="pc-link">
                <span class="pc-micon">
                  <img src="${iconUrl}" style="width: 19px;" alt="Icon" class="icon">
                </span>
                <span class="pc-mtext" id="${menu.active_ids}">${menu.menu_name}</span>
              </a>
            </li>
          `;
        }
      }
    });
  
    // Update the DOM if container exists
    const menuContainer = $(".menuupdatesall");
    if (menuContainer.length > 0) {
      menuContainer.html(menuHTML);
  
      // Enable dropdown toggling
      menuContainer.off("click").on("click", ".pc-hasmenu > .pc-link", function (e) {
        e.preventDefault();
        $(this).next(".pc-submenu").slideToggle();
        $(this).parent().toggleClass("open");
      });
    } else {
      console.error("Menu container '.menuupdatesall' not found in DOM.");
    }
  }
  
  
  
  
  
  // function handleSuccessmenu(responseData) {
    
  //   let menuHTML = "";
  //   const currentPath = window.location.pathname;
  
  //   $.each(responseData, function (index, menu) {
  
  // // console.log(responseData);
  
  //     const iconUrl = `/assets/images/${menu.icon}`;
  //     let isActiveDropdown = false;
  //     let submenuHTML = "";
      
  //     // console.log(menu.dropdown);
      
  //     // Check if it's a dropdown and has children
  //     if (menu.dropdown == 1 && menu.main_menu == 1) {
  //       if (menu.children && menu.children.length > 0) {
  //         $.each(menu.children, function (i, child) {
  //           const childUrl = '/' + child.route.replace(/\./g, '/');
  //           const isChildActive = currentPath === childUrl;
  
  //           if (isChildActive) {
  //             isActiveDropdown = true;
  //           }
  
  //           submenuHTML += `
  //             <li class="pc-item ${isChildActive ? 'active' : ''}">
  //               <a href="${childUrl}" class="pc-link">${child.menu_name}</a>
  //             </li>
  //           `;
  //         });
  //       }
  
  //       menuHTML += `
  //         <li class="pc-item pc-hasmenu ${isActiveDropdown ? 'active open' : ''}">
  //           <a href="javascript:void(0);" class="pc-link">
  //             <span class="pc-micon">
  //               <img src="${iconUrl}" style="width: 22px;" alt="Icon" class="icon">
  //             </span>
  //             <span class="pc-mtext">${menu.menu_name}</span>
  //           </a>
  //           <ul class="pc-submenu" style="${isActiveDropdown ? 'display: block;' : 'display: none;'}">
  //             ${submenuHTML}
  //           </ul>
  //         </li>
  //       `;
  
  //     } else if (menu.main_menu == 1 && menu.dropdown == 0) {
  //       // Main menu without dropdown
  //       const routeUrl = '/' + menu.route.replace(/\./g, '/');
  //       const isActive = currentPath === routeUrl;
  
  //       menuHTML += `
  //         <li class="pc-item ${isActive ? 'active' : ''}">
  //           <a href="${routeUrl}" class="pc-link">
  //             <span class="pc-micon">
  //               <img src="${iconUrl}" style="width: 19px;" alt="Icon" class="icon">
  //             </span>
  //             <span class="pc-mtext" id="${menu.active_ids}">${menu.menu_name}</span>
  //           </a>
  //         </li>
  //       `;
  //     }
  
  //     // Don't render submenus (main_menu == 0) as top-level items
  //   });
  
  
  
  //   $(".menuupdatesall").html(menuHTML);
  
  //   // Toggle dropdown menu on click
  //   $(".menuupdatesall").on("click", ".pc-hasmenu > .pc-link", function (e) {
  //     e.preventDefault();
  //     $(this).next(".pc-submenu").slideToggle();
  //     $(this).parent().toggleClass("open");
  //   });
  // }
  
  
  
  // Handle AJAX error
  function handleErrormenu(xhr, status, error) {
    console.error("AJAX Error:", status, error);
  }
  