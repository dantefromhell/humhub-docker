<?php

/**
 * HumhubController implements a 'yii' console command to generate the 'common.php'
 * configuration file by deep merging arrays read from multipe files in the
 * 'common.d/' directory.
 *
 * To use this command, run the following command:
 * ```bash
 * php yii humhub/cfg-gen-common
 * ```
 *
 * This code is based on a conversation about config loaders in the HumHub community
 * https://community.humhub.com/s/installation-and-setup/wiki/Configuration+Examples#change-user-display-name
 */

namespace humhub\commands;

use yii\helpers\Console;
use humhub\modules\file\libs\FileHelper;
use yii\console\Controller;

class HumhubController extends Controller
{
    /**
     * Auto-Generate the common.php configuration file from files in 'common.d/' folder
     */
    public function actionCfgGenCommon()
    {
        // Define the path for the common.php file
        $commonFilePath = '@app/config/common.php';

        // Load and merge configuration files from the common.d directory
        $result = $this->loadConfigFiles('@app/config/common.d');
        
        // Save the merged configuration to the common.php file using standard file writing
        $commonContent = '<?php return ' . var_export($result, true) . ";\n";
        FileHelper::createDirectory(dirname(\Yii::getAlias($commonFilePath)), 0777, true);
        file_put_contents(\Yii::getAlias($commonFilePath), $commonContent);

        // Display success message in the console
        $this->stdout("common.php file generated successfully.\n", Console::FG_GREEN);
    }

    /**
     * Recursively merge arrays without overwriting existing values without notice.
     *
     * @param array ...$arrays Arrays to be merged
     * @return array The merged array
     */
    private function customMergeArrays(array ...$arrays): array
    {
        $merged = [];
        foreach ($arrays as $array) {
            foreach ($array as $key => $value) {
                if (is_array($value) && isset($merged[$key]) && is_array($merged[$key])) {
                    $merged[$key] = $this->customMergeArrays($merged[$key], $value);
                } elseif (!isset($merged[$key])) {
                    $merged[$key] = $value;
                }
            }
        }

        return $merged;
    }

    /**
     * Load configuration files from a directory and merge them.
     *
     * @param string $directory The directory containing configuration files
     * @return array The merged configuration
     */
    private function loadConfigFiles(string $directory): array
    {
        // Find all PHP files in the specified directory
        $fileList = FileHelper::findFiles(\Yii::getAlias($directory), ['only' => ['*.php']]);
        $result = [];

        // Load and merge each configuration file
        foreach ($fileList as $filename) {
            $includedData = require $filename;
            $result = $this->customMergeArrays($result, $includedData);
        }

        return $result;
    }
}
