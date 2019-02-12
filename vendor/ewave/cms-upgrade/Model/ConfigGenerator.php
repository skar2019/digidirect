<?php
namespace Ewave\CmsUpgrade\Model;

/**
 * Class ConfigGenerator
 * @package Ewave\CmsUpgrade\Model
 */
class ConfigGenerator extends Generator
{
    /**
     * @param array $params
     * @return \Magento\Framework\DataObject
     */
    public function processUpgradeScript($params)
    {
        $generateGroups = [];
        $checked = array_keys($params['checked']);
        foreach ($checked as $filedName) {
            $filed = str_replace($params['section'] . '_', '', $filedName);
            if (isset($params['groups'][$filed])) {
                $generateGroups[$filed] = $params['groups'][$filed];
            }
        }
        $data = $this->_getUpgradeData();
        $configData = [
            'section' => $params['section'],
            'website' => $params['website'],
            'store' => $params['store'],
            'groups' => $this->_getGroupsForSave($generateGroups, $params['files']),
        ];
        $data['items'][] = $configData;
        $nextVersion = $this->_getNextModuleVersion();
        $put = $this->putUpgradeFile($data, $nextVersion);
        if ($put) {
            $this->_changeDbVersion($nextVersion);
        }
        return $this->_result;
    }

    /**
     * Process nested groups
     *
     * @param mixed $group
     * @return array
     */
    protected function _processNestedGroups($group)
    {
        $data = [];

        if (isset($group['fields']) && is_array($group['fields'])) {
            foreach ($group['fields'] as $fieldName => $field) {
                if (!empty($field['value'])) {
                    $data['fields'][$fieldName] = ['value' => $field['value']];
                }
            }
        }

        if (isset($group['groups']) && is_array($group['groups'])) {
            foreach ($group['groups'] as $groupName => $groupData) {
                $nestedGroup = $this->_processNestedGroups($groupData);
                if (!empty($nestedGroup)) {
                    $data['groups'][$groupName] = $nestedGroup;
                }
            }
        }

        return $data;
    }

    /**
     * Get groups for upgrade
     *
     * @param array $groups
     * @param array $files
     * @return mixed
     */
    protected function _getGroupsForSave($groups, $files)
    {
        if ($files && is_array($files)) {
            foreach ($files as $groupName => $group) {
                $data = $this->_processNestedGroups($group);
                if (!empty($data)) {
                    if (!empty($groups[$groupName])) {
                        $groups[$groupName] = array_merge_recursive((array)$groups[$groupName], $data);
                    } else {
                        $groups[$groupName] = $data;
                    }
                }
            }
        }
        return $groups;
    }
}
