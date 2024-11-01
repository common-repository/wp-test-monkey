var WPTestMonkey = {
  basePath: '',
  
  trackGoal: function(id, obj) {
    // obj can be a link
    if (obj != undefined) {
      if (obj.href != undefined) {
        // it's a link
        obj.href = WPTestMonkey.basePath+'/track_goal.php?id='+id+'&type=link&url='+escape(obj.href);
      };
    } else {
      jQuery.get(WPTestMonkey.basePath+'/track_goal.php?id='+id);
    }
  },
  
    trackProjectGoal: function(id, obj) {
    // obj can be a link
    if (obj != undefined) {
      if (obj.href != undefined) {
        // it's a link
        obj.href = WPTestMonkey.basePath+'/track_project_goal.php?id='+id+'&type=link&url='+escape(obj.href);
      };
    } else {
      jQuery.get(WPTestMonkey.basePath+'/track_project_goal.php?id='+id);
    }
  },
  
  trackVariation: function(id) {
    jQuery.get(WPTestMonkey.basePath+'/track_variation.php?id='+id);
  }
};