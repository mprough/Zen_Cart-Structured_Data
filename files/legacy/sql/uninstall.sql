# SuperData 3.0.1 legacy uninstaller
# This removes SuperData's settings. It does not remove former PLUGIN_SDATA_* settings.

DELETE FROM admin_pages WHERE page_key = 'configSuperData';
DELETE FROM configuration WHERE configuration_key LIKE 'PLUGIN_SUPERDATA_%';
DELETE FROM configuration_group
WHERE configuration_group_title = 'SuperData'
  AND NOT EXISTS (
      SELECT 1 FROM configuration
      WHERE configuration.configuration_group_id = configuration_group.configuration_group_id
  );
