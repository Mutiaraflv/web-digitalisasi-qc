<?php

namespace App\Services;

use ZipArchive;
use DateTime;
use Exception;

class ExcelReportService
{
    /**
     * Convert YYYY-MM-DD date to Excel Serial number
     */
    public static function dateToSerial(?string $dateStr): ?int
    {
        if (empty($dateStr)) {
            return null;
        }

        try {
            $d = new DateTime($dateStr);
            $excelBase = new DateTime('1899-12-30');
            $diff = $d->diff($excelBase);
            return (int) $diff->format('%a');
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Replace or set cell value in SpreadsheetML XML
     */
    public static function setCellValue(string $xml, string $cellRef, $value, bool $isNumeric = false): string
    {
        $pattern = '/(<c\s+r="' . preg_quote($cellRef, '/') . '"[^>]*>)([\s\S]*?)(<\/c>)|(<c\s+r="' . preg_quote($cellRef, '/') . '"[^>]*\/>)/';

        return preg_replace_callback($pattern, function ($matches) use ($value, $isNumeric) {
            $openTag = !empty($matches[1]) ? $matches[1] : $matches[4];
            
            // Clean out existing t="..." and self-closing slash
            $tag = preg_replace('/\s+t="[^"]*"/', '', $openTag);
            $tag = preg_replace('/\/>$/', '>', trim($tag));
            if (substr($tag, -1) === '>') {
                $tag = substr($tag, 0, -1);
            }

            if ($value === null || $value === '') {
                $value = '';
            }

            if ($isNumeric && $value !== '' && is_numeric($value)) {
                return $tag . '><v>' . $value . '</v></c>';
            } else {
                $safeVal = htmlspecialchars((string) $value, ENT_XML1, 'UTF-8');
                return $tag . ' t="inlineStr"><is><t>' . $safeVal . '</t></is></c>';
            }
        }, $xml);
    }

    /**
     * Generate Excel file from Report model
     * @return string Path to temporary generated file
     */
    public function generateExcelFile($report): string
    {
        $templatePath = storage_path('app/templates/template.xlsx');
        if (!file_exists($templatePath)) {
            throw new Exception("Excel template not found at: " . $templatePath);
        }

        $tempDir = storage_path('app/temp');
        if (!file_exists($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        $filename = 'CCR_' . preg_replace('/[^A-Za-z0-9_-]/', '_', $report->ssc_id ?? 'REPORT') . '_' . time() . '.xlsx';
        $outputPath = $tempDir . DIRECTORY_SEPARATOR . $filename;

        // Copy template to destination
        copy($templatePath, $outputPath);

        $zip = new ZipArchive();
        if ($zip->open($outputPath) !== true) {
            throw new Exception("Unable to open Excel archive");
        }

        $xml = $zip->getFromName('xl/worksheets/sheet1.xml');
        if (!$xml) {
            $zip->close();
            throw new Exception("Could not find sheet1.xml in template");
        }

        // 1. General Header
        if ($report->report_no !== null) $xml = self::setCellValue($xml, 'H5', $report->report_no);
        if ($report->date) {
            $dateSerial = self::dateToSerial($report->date instanceof DateTime ? $report->date->format('Y-m-d') : (string) $report->date);
            if ($dateSerial) $xml = self::setCellValue($xml, 'P5', $dateSerial, true);
        }
        if ($report->customer !== null) $xml = self::setCellValue($xml, 'AE5', $report->customer);
        if ($report->part_name !== null) $xml = self::setCellValue($xml, 'AX5', $report->part_name);
        if ($report->job_no !== null) $xml = self::setCellValue($xml, 'AE6', $report->job_no);
        if ($report->model_unit !== null) $xml = self::setCellValue($xml, 'AX6', $report->model_unit);
        if ($report->ssc_id !== null) $xml = self::setCellValue($xml, 'H7', $report->ssc_id);
        if ($report->quote_no !== null) $xml = self::setCellValue($xml, 'AE7', $report->quote_no);
        if ($report->part_no !== null) $xml = self::setCellValue($xml, 'AX7', $report->part_no);

        $sections = is_array($report->sections) ? $report->sections : json_decode($report->sections ?? '[]', true);

        // Section 01
        if (!empty($sections['item01'])) {
            $i1 = $sections['item01'];
            if (isset($i1['x'])) $xml = self::setCellValue($xml, 'AR42', $i1['x'], true);
            if (isset($i1['y'])) $xml = self::setCellValue($xml, 'AV42', $i1['y'], true);
            if (isset($i1['z'])) $xml = self::setCellValue($xml, 'AZ42', $i1['z'], true);
            if (isset($i1['ar'])) $xml = self::setCellValue($xml, 'BD42', $i1['ar']);
            if (isset($i1['remark1'])) $xml = self::setCellValue($xml, 'AM43', $i1['remark1']);
            if (isset($i1['remark2'])) $xml = self::setCellValue($xml, 'BP43', $i1['remark2']);
            if (isset($i1['remark3'])) $xml = self::setCellValue($xml, 'AM45', $i1['remark3']);
            if (isset($i1['remark4'])) $xml = self::setCellValue($xml, 'AM46', $i1['remark4']);
            if (isset($i1['remark5'])) $xml = self::setCellValue($xml, 'AM47', $i1['remark5']);
            if (isset($i1['remark6'])) $xml = self::setCellValue($xml, 'AM48', $i1['remark6']);
            if (isset($i1['lengthResult'])) $xml = self::setCellValue($xml, 'AR53', $i1['lengthResult'], true);
            if (isset($i1['lengthAr'])) $xml = self::setCellValue($xml, 'BD53', $i1['lengthAr']);
            if (isset($i1['penetrantRemark'])) $xml = self::setCellValue($xml, 'AM67', $i1['penetrantRemark']);
        }

        // Section 02
        if (!empty($sections['item02'])) {
            $i2 = $sections['item02'];
            if (!empty($i2['itemB'])) {
                $b = $i2['itemB'];
                if (isset($b['row1']['x'])) $xml = self::setCellValue($xml, 'AR82', $b['row1']['x'], true);
                if (isset($b['row1']['y'])) $xml = self::setCellValue($xml, 'AV82', $b['row1']['y'], true);
                if (isset($b['row1']['z'])) $xml = self::setCellValue($xml, 'AZ82', $b['row1']['z'], true);
                if (isset($b['row1']['ar'])) $xml = self::setCellValue($xml, 'BD82', $b['row1']['ar']);

                if (isset($b['row2']['x'])) $xml = self::setCellValue($xml, 'AR83', $b['row2']['x'], true);
                if (isset($b['row2']['y'])) $xml = self::setCellValue($xml, 'AV83', $b['row2']['y'], true);
                if (isset($b['row2']['z'])) $xml = self::setCellValue($xml, 'AZ83', $b['row2']['z'], true);
                if (isset($b['row2']['ar'])) $xml = self::setCellValue($xml, 'BD83', $b['row2']['ar']);

                if (isset($b['row3']['x'])) $xml = self::setCellValue($xml, 'AR84', $b['row3']['x'], true);
                if (isset($b['row3']['y'])) $xml = self::setCellValue($xml, 'AV84', $b['row3']['y'], true);
                if (isset($b['row3']['z'])) $xml = self::setCellValue($xml, 'AZ84', $b['row3']['z'], true);
                if (isset($b['row3']['ar'])) $xml = self::setCellValue($xml, 'BD84', $b['row3']['ar']);
            }
            if (!empty($i2['itemC'])) {
                $c = $i2['itemC'];
                if (isset($c['row1']['x'])) $xml = self::setCellValue($xml, 'AR85', $c['row1']['x'], true);
                if (isset($c['row1']['y'])) $xml = self::setCellValue($xml, 'AV85', $c['row1']['y'], true);
                if (isset($c['row1']['z'])) $xml = self::setCellValue($xml, 'AZ85', $c['row1']['z'], true);
                if (isset($c['row1']['ar'])) $xml = self::setCellValue($xml, 'BD85', $c['row1']['ar']);

                if (isset($c['row2']['x'])) $xml = self::setCellValue($xml, 'AR86', $c['row2']['x'], true);
                if (isset($c['row2']['y'])) $xml = self::setCellValue($xml, 'AV86', $c['row2']['y'], true);
                if (isset($c['row2']['z'])) $xml = self::setCellValue($xml, 'AZ86', $c['row2']['z'], true);
                if (isset($c['row2']['ar'])) $xml = self::setCellValue($xml, 'BD86', $c['row2']['ar']);

                if (isset($c['row3']['x'])) $xml = self::setCellValue($xml, 'AR87', $c['row3']['x'], true);
                if (isset($c['row3']['y'])) $xml = self::setCellValue($xml, 'AV87', $c['row3']['y'], true);
                if (isset($c['row3']['z'])) $xml = self::setCellValue($xml, 'AZ87', $c['row3']['z'], true);
                if (isset($c['row3']['ar'])) $xml = self::setCellValue($xml, 'BD87', $c['row3']['ar']);
            }
            if (!empty($i2['remarks']) && is_array($i2['remarks'])) {
                if (isset($i2['remarks'][0])) $xml = self::setCellValue($xml, 'AM88', $i2['remarks'][0]);
                if (isset($i2['remarks'][1])) $xml = self::setCellValue($xml, 'AM90', $i2['remarks'][1]);
                if (isset($i2['remarks'][2])) $xml = self::setCellValue($xml, 'BO90', $i2['remarks'][2]);
                if (isset($i2['remarks'][3])) $xml = self::setCellValue($xml, 'AM91', $i2['remarks'][3]);
            }
            if (isset($i2['penetrantRemark'])) $xml = self::setCellValue($xml, 'AM105', $i2['penetrantRemark']);
        }

        // Section 03
        if (!empty($sections['item03'])) {
            $i3 = $sections['item03'];
            if (!empty($i3['itemD'])) {
                if (isset($i3['itemD']['x'])) $xml = self::setCellValue($xml, 'AR120', $i3['itemD']['x'], true);
                if (isset($i3['itemD']['y'])) $xml = self::setCellValue($xml, 'AV120', $i3['itemD']['y'], true);
                if (isset($i3['itemD']['z'])) $xml = self::setCellValue($xml, 'AZ120', $i3['itemD']['z'], true);
                if (isset($i3['itemD']['ar'])) $xml = self::setCellValue($xml, 'BD120', $i3['itemD']['ar']);
            }
            if (!empty($i3['distanceE'])) {
                if (isset($i3['distanceE']['x'])) $xml = self::setCellValue($xml, 'AR127', $i3['distanceE']['x'], true);
                if (isset($i3['distanceE']['y'])) $xml = self::setCellValue($xml, 'AV127', $i3['distanceE']['y'], true);
                if (isset($i3['distanceE']['z'])) $xml = self::setCellValue($xml, 'AZ127', $i3['distanceE']['z'], true);
                if (isset($i3['distanceE']['ar'])) $xml = self::setCellValue($xml, 'BD127', $i3['distanceE']['ar']);
            }
            if (!empty($i3['remarks']) && is_array($i3['remarks'])) {
                if (isset($i3['remarks'][0])) $xml = self::setCellValue($xml, 'AM121', $i3['remarks'][0]);
                if (isset($i3['remarks'][1])) $xml = self::setCellValue($xml, 'AM123', $i3['remarks'][1]);
                if (isset($i3['remarks'][2])) $xml = self::setCellValue($xml, 'AM128', $i3['remarks'][2]);
                if (isset($i3['remarks'][3])) $xml = self::setCellValue($xml, 'AM129', $i3['remarks'][3]);
            }
            if (isset($i3['penetrantRemark'])) $xml = self::setCellValue($xml, 'AM143', $i3['penetrantRemark']);
        }

        // Section 04
        if (!empty($sections['item04'])) {
            $i4 = $sections['item04'];
            if (!empty($i4['itemF'])) {
                $f = $i4['itemF'];
                if (isset($f['row1']['x'])) $xml = self::setCellValue($xml, 'AR158', $f['row1']['x'], true);
                if (isset($f['row1']['y'])) $xml = self::setCellValue($xml, 'AV158', $f['row1']['y'], true);
                if (isset($f['row1']['z'])) $xml = self::setCellValue($xml, 'AZ158', $f['row1']['z'], true);
                if (isset($f['row1']['ar'])) $xml = self::setCellValue($xml, 'BD158', $f['row1']['ar']);

                if (isset($f['row2']['x'])) $xml = self::setCellValue($xml, 'AR159', $f['row2']['x'], true);
                if (isset($f['row2']['y'])) $xml = self::setCellValue($xml, 'AV159', $f['row2']['y'], true);
                if (isset($f['row2']['z'])) $xml = self::setCellValue($xml, 'AZ159', $f['row2']['z'], true);
                if (isset($f['row2']['ar'])) $xml = self::setCellValue($xml, 'BD159', $f['row2']['ar']);

                if (isset($f['row3']['x'])) $xml = self::setCellValue($xml, 'AR160', $f['row3']['x'], true);
                if (isset($f['row3']['y'])) $xml = self::setCellValue($xml, 'AV160', $f['row3']['y'], true);
                if (isset($f['row3']['z'])) $xml = self::setCellValue($xml, 'AZ160', $f['row3']['z'], true);
                if (isset($f['row3']['ar'])) $xml = self::setCellValue($xml, 'BD160', $f['row3']['ar']);
            }
            if (!empty($i4['distanceK'])) {
                if (isset($i4['distanceK']['result'])) $xml = self::setCellValue($xml, 'AR169', $i4['distanceK']['result'], true);
                if (isset($i4['distanceK']['ar'])) $xml = self::setCellValue($xml, 'BD169', $i4['distanceK']['ar']);
            }
            if (!empty($i4['remarks']) && is_array($i4['remarks'])) {
                if (isset($i4['remarks'][0])) $xml = self::setCellValue($xml, 'AM161', $i4['remarks'][0]);
                if (isset($i4['remarks'][1])) $xml = self::setCellValue($xml, 'AM163', $i4['remarks'][1]);
            }
            if (isset($i4['penetrantRemark'])) $xml = self::setCellValue($xml, 'AM181', $i4['penetrantRemark']);
        }

        // Section 05
        if (!empty($sections['item05'])) {
            $i5 = $sections['item05'];
            if (!empty($i5['itemH'])) {
                $h = $i5['itemH'];
                if (isset($h['row1']['x'])) $xml = self::setCellValue($xml, 'AR196', $h['row1']['x'], true);
                if (isset($h['row1']['y'])) $xml = self::setCellValue($xml, 'AV196', $h['row1']['y'], true);
                if (isset($h['row1']['z'])) $xml = self::setCellValue($xml, 'AZ196', $h['row1']['z'], true);
                if (isset($h['row1']['ar'])) $xml = self::setCellValue($xml, 'BD196', $h['row1']['ar']);

                if (isset($h['row2']['x'])) $xml = self::setCellValue($xml, 'AR197', $h['row2']['x'], true);
                if (isset($h['row2']['y'])) $xml = self::setCellValue($xml, 'AV197', $h['row2']['y'], true);
                if (isset($h['row2']['z'])) $xml = self::setCellValue($xml, 'AZ197', $h['row2']['z'], true);
                if (isset($h['row2']['ar'])) $xml = self::setCellValue($xml, 'BD197', $h['row2']['ar']);

                if (isset($h['row3']['x'])) $xml = self::setCellValue($xml, 'AR198', $h['row3']['x'], true);
                if (isset($h['row3']['y'])) $xml = self::setCellValue($xml, 'AV198', $h['row3']['y'], true);
                if (isset($h['row3']['z'])) $xml = self::setCellValue($xml, 'AZ198', $h['row3']['z'], true);
                if (isset($h['row3']['ar'])) $xml = self::setCellValue($xml, 'BD198', $h['row3']['ar']);
            }
            if (!empty($i5['distanceJ'])) {
                if (isset($i5['distanceJ']['result'])) $xml = self::setCellValue($xml, 'AR207', $i5['distanceJ']['result'], true);
                if (isset($i5['distanceJ']['ar'])) $xml = self::setCellValue($xml, 'BD207', $i5['distanceJ']['ar']);
            }
            if (!empty($i5['remarks']) && is_array($i5['remarks'])) {
                if (isset($i5['remarks'][0])) $xml = self::setCellValue($xml, 'AM199', $i5['remarks'][0]);
                if (isset($i5['remarks'][1])) $xml = self::setCellValue($xml, 'AM201', $i5['remarks'][1]);
            }
            if (isset($i5['penetrantRemark'])) $xml = self::setCellValue($xml, 'AM219', $i5['penetrantRemark']);
        }

        // Section 06
        if (!empty($sections['item06'])) {
            $i6 = $sections['item06'];
            if (!empty($i6['itemG'])) {
                if (isset($i6['itemG']['x'])) $xml = self::setCellValue($xml, 'AR234', $i6['itemG']['x'], true);
                if (isset($i6['itemG']['y'])) $xml = self::setCellValue($xml, 'AV234', $i6['itemG']['y'], true);
                if (isset($i6['itemG']['z'])) $xml = self::setCellValue($xml, 'AZ234', $i6['itemG']['z'], true);
                if (isset($i6['itemG']['ar'])) $xml = self::setCellValue($xml, 'BD234', $i6['itemG']['ar']);
            }
            if (!empty($i6['distanceI'])) {
                if (isset($i6['distanceI']['result'])) $xml = self::setCellValue($xml, 'AR245', $i6['distanceI']['result'], true);
                if (isset($i6['distanceI']['ar'])) $xml = self::setCellValue($xml, 'BD245', $i6['distanceI']['ar']);
            }
            if (!empty($i6['remarks']) && is_array($i6['remarks'])) {
                if (isset($i6['remarks'][0])) $xml = self::setCellValue($xml, 'AM235', $i6['remarks'][0]);
                if (isset($i6['remarks'][1])) $xml = self::setCellValue($xml, 'BS236', $i6['remarks'][1]);
                if (isset($i6['remarks'][2])) $xml = self::setCellValue($xml, 'AM237', $i6['remarks'][2]);
                if (isset($i6['remarks'][3])) $xml = self::setCellValue($xml, 'AM238', $i6['remarks'][3]);
                if (isset($i6['remarks'][4])) $xml = self::setCellValue($xml, 'AM239', $i6['remarks'][4]);
                if (isset($i6['remarks'][5])) $xml = self::setCellValue($xml, 'AM240', $i6['remarks'][5]);
            }
            if (isset($i6['penetrantRemark'])) $xml = self::setCellValue($xml, 'AM257', $i6['penetrantRemark']);
        }

        // TIR
        $tir = is_array($report->tir) ? $report->tir : json_decode($report->tir ?? '[]', true);
        if (!empty($tir)) {
            if (!empty($tir['datum'])) $xml = self::setCellValue($xml, 'Q283', $tir['datum']);
            $tirRows = [277, 278, 279, 280, 281, 282];
            if (!empty($tir['items']) && is_array($tir['items'])) {
                foreach ($tir['items'] as $idx => $item) {
                    if ($idx < count($tirRows)) {
                        $r = $tirRows[$idx];
                        if (isset($item['d0'])) $xml = self::setCellValue($xml, "X{$r}", $item['d0']);
                        if (isset($item['d90'])) $xml = self::setCellValue($xml, "AB{$r}", $item['d90']);
                        if (isset($item['d180'])) $xml = self::setCellValue($xml, "AF{$r}", $item['d180']);
                        if (isset($item['d270'])) $xml = self::setCellValue($xml, "AJ{$r}", $item['d270']);
                        if (isset($item['ar'])) $xml = self::setCellValue($xml, "AN{$r}", $item['ar']);
                    }
                }
            }
        }

        // Recommendations Table (Rows 287 to 303)
        $recs = is_array($report->recommendations) ? $report->recommendations : json_decode($report->recommendations ?? '[]', true);
        if (!empty($recs) && is_array($recs)) {
            $recRows = [287, 288, 289, 290, 291, 292, 293, 294, 295, 297, 298, 299, 300, 301, 302, 303];
            foreach ($recs as $idx => $rec) {
                if ($idx < count($recRows)) {
                    $r = $recRows[$idx];
                    if (isset($rec['item'])) $xml = self::setCellValue($xml, "A{$r}", $rec['item']);
                    if (isset($rec['remarks'])) $xml = self::setCellValue($xml, "E{$r}", $rec['remarks']);
                    if (isset($rec['recommendation'])) $xml = self::setCellValue($xml, "AH{$r}", $rec['recommendation']);
                }
            }
        }

        // Signatures
        $sig = is_array($report->signatures) ? $report->signatures : json_decode($report->signatures ?? '[]', true);
        if (!empty($sig)) {
            if (!empty($sig['checkedBy'])) {
                if (isset($sig['checkedBy']['name'])) $xml = self::setCellValue($xml, 'A341', $sig['checkedBy']['name']);
                if (!empty($sig['checkedBy']['date'])) {
                    $ds = self::dateToSerial($sig['checkedBy']['date']);
                    if ($ds) $xml = self::setCellValue($xml, 'L341', $ds, true);
                }
            }
            if (!empty($sig['createdBy'])) {
                if (isset($sig['createdBy']['name'])) $xml = self::setCellValue($xml, 'V341', $sig['createdBy']['name']);
                if (!empty($sig['createdBy']['date'])) {
                    $ds = self::dateToSerial($sig['createdBy']['date']);
                    if ($ds) $xml = self::setCellValue($xml, 'AF341', $ds, true);
                }
            }
            if (!empty($sig['reviewedBy'])) {
                if (isset($sig['reviewedBy']['name'])) $xml = self::setCellValue($xml, 'AO341', $sig['reviewedBy']['name']);
                if (!empty($sig['reviewedBy']['date'])) {
                    $ds = self::dateToSerial($sig['reviewedBy']['date']);
                    if ($ds) $xml = self::setCellValue($xml, 'AX341', $ds, true);
                }
            }
        }

        $zip->addFromString('xl/worksheets/sheet1.xml', $xml);
        $zip->close();

        return $outputPath;
    }
}

