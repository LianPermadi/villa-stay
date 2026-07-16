<?php

namespace App\Support;

use RuntimeException;
use Shuchkin\SimpleXLSXGen;

final class XlsxWorkbook
{
    private array $sheets = [];

    public function addSheet(
        string $name,
        array $rows,
        array $widths = [],
        array $merges = [],
        ?string $freezeAt = null,
        ?string $autoFilter = null
    ): void {
        $this->sheets[] = compact('name', 'rows', 'widths', 'merges', 'freezeAt', 'autoFilter');
    }

    public function save(string $path): void
    {
        if ($this->sheets === []) {
            throw new RuntimeException('Workbook harus memiliki minimal satu sheet.');
        }

        $directory = dirname($path);
        if (! is_dir($directory) && ! mkdir($directory, 0775, true) && ! is_dir($directory)) {
            throw new RuntimeException('Folder laporan tidak dapat dibuat.');
        }

        @unlink($path);

        require_once __DIR__.'/ThirdParty/SimpleXLSXGen.php';
        $xlsx = new SimpleXLSXGen;
        foreach ($this->sheets as $sheet) {
            $xlsx->addSheet($this->libraryRows($sheet['rows']), $this->safeSheetName($sheet['name']));
            foreach ($sheet['widths'] as $column => $width) {
                $xlsx->setColWidth($column, $width);
            }
            foreach ($sheet['merges'] as $merge) {
                $xlsx->mergeCells($merge);
            }
            if ($sheet['freezeAt']) {
                $xlsx->freezePanes($sheet['freezeAt']);
            }
            if ($sheet['autoFilter']) {
                $xlsx->autoFilter($sheet['autoFilter']);
            }
        }
        $xlsx->setTitle('Laporan Keuangan Villa-Sina')->setAuthor('Villa-Sina');

        if (! $xlsx->saveAs($path)) {
            throw new RuntimeException('File Excel tidak dapat disimpan.');
        }
    }

    private function libraryRows(array $rows): array
    {
        $result = [];
        foreach ($rows as $cells) {
            $maxColumn = 1;
            foreach (array_keys($cells) as $column) {
                $maxColumn = max($maxColumn, $this->columnNumber($column));
            }
            $row = array_fill(0, $maxColumn, '');
            foreach ($cells as $column => $cell) {
                $cell = is_array($cell) ? $cell : ['value' => $cell];
                $row[$this->columnNumber($column) - 1] = $this->libraryCell($cell);
            }
            $result[] = $row;
        }

        return $result;
    }

    private function libraryCell(array $cell)
    {
        $value = $cell['value'] ?? '';
        $style = (int) ($cell['style'] ?? 0);
        $isNumber = ($cell['type'] ?? null) === 'number' || is_int($value) || is_float($value);
        $content = isset($cell['formula'])
            ? '<f v="'.(float) $value.'">'.htmlspecialchars($cell['formula'], ENT_QUOTES, 'UTF-8').'</f>'
            : ($isNumber ? (float) $value : htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8'));

        $definitions = [
            0 => '',
            1 => ' bgcolor="#2D5A27" color="#FFFFFF" font-size="16"',
            2 => '',
            3 => ' bgcolor="#11BFE3" color="#FFFFFF"',
            4 => ' bgcolor="#2D5A27" color="#FFFFFF"',
            5 => '',
            6 => ' nf="#,##0.00;[Red]-#,##0.00"',
            7 => ' bgcolor="#C9A962" nf="#,##0.00;[Red]-#,##0.00"',
            8 => ' bgcolor="#C9A962"',
            9 => ' bgcolor="#F3F4F6"',
            10 => ' bgcolor="#F3F4F6" nf="#,##0.00;[Red]-#,##0.00"',
            11 => ' bgcolor="#FEE2E2" nf="#,##0.00;[Red]-#,##0.00"',
        ];
        $bold = in_array($style, [1, 2, 3, 4, 7, 8], true);
        $center = in_array($style, [1, 3, 4], true);

        return '<style'.($definitions[$style] ?? '').'>'
            .($bold ? '<b>' : '')
            .($center ? '<center><middle><wraptext>' : '')
            .$content
            .'</style>';
    }

    private function files(): array
    {
        $files = [
            '[Content_Types].xml' => $this->contentTypes(),
            '_rels/.rels' => $this->packageRelationships(),
            'docProps/app.xml' => $this->appProperties(),
            'docProps/core.xml' => $this->coreProperties(),
            'xl/workbook.xml' => $this->workbook(),
            'xl/_rels/workbook.xml.rels' => $this->workbookRelationships(),
            'xl/styles.xml' => $this->styles(),
        ];

        foreach ($this->sheets as $index => $sheet) {
            $files['xl/worksheets/sheet'.($index + 1).'.xml'] = $this->worksheet($sheet);
        }

        return $files;
    }

    private function contentTypes(): string
    {
        $overrides = '';
        foreach (array_keys($this->sheets) as $index) {
            $overrides .= '<Override PartName="/xl/worksheets/sheet'.($index + 1).'.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>';
        }

        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
            .'<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
            .'<Default Extension="xml" ContentType="application/xml"/>'
            .'<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'
            .'<Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>'
            .'<Override PartName="/docProps/core.xml" ContentType="application/vnd.openxmlformats-package.core-properties+xml"/>'
            .'<Override PartName="/docProps/app.xml" ContentType="application/vnd.openxmlformats-officedocument.extended-properties+xml"/>'
            .$overrides.'</Types>';
    }

    private function packageRelationships(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            .'<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>'
            .'<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/package/2006/relationships/metadata/core-properties" Target="docProps/core.xml"/>'
            .'<Relationship Id="rId3" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/extended-properties" Target="docProps/app.xml"/>'
            .'</Relationships>';
    }

    private function workbook(): string
    {
        $sheets = '';
        foreach ($this->sheets as $index => $sheet) {
            $sheets .= '<sheet name="'.$this->xml($this->safeSheetName($sheet['name'])).'" sheetId="'.($index + 1).'" r:id="rId'.($index + 1).'"/>';
        }

        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
            .'<bookViews><workbookView/></bookViews><sheets>'.$sheets.'</sheets><calcPr calcId="191029" fullCalcOnLoad="1"/></workbook>';
    }

    private function workbookRelationships(): string
    {
        $relationships = '';
        foreach (array_keys($this->sheets) as $index) {
            $relationships .= '<Relationship Id="rId'.($index + 1).'" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet'.($index + 1).'.xml"/>';
        }
        $relationships .= '<Relationship Id="rId'.(count($this->sheets) + 1).'" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>';

        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'.$relationships.'</Relationships>';
    }

    private function worksheet(array $sheet): string
    {
        $rows = '';
        foreach ($sheet['rows'] as $rowNumber => $cells) {
            $rowIndex = $rowNumber + 1;
            $cellXml = '';
            foreach ($cells as $column => $cell) {
                $cell = is_array($cell) ? $cell : ['value' => $cell];
                $value = $cell['value'] ?? '';
                $style = (int) ($cell['style'] ?? 0);
                $reference = strtoupper($column).$rowIndex;
                $formula = isset($cell['formula']) ? '<f>'.$this->xml($cell['formula']).'</f>' : '';

                if (($cell['type'] ?? null) === 'number' || is_int($value) || is_float($value)) {
                    $cellXml .= '<c r="'.$reference.'" s="'.$style.'">'.$formula.'<v>'.(is_numeric($value) ? $value : 0).'</v></c>';
                } else {
                    $cellXml .= '<c r="'.$reference.'" s="'.$style.'" t="inlineStr"><is><t xml:space="preserve">'.$this->xml((string) $value).'</t></is></c>';
                }
            }
            $rows .= '<row r="'.$rowIndex.'">'.$cellXml.'</row>';
        }

        $columns = '';
        foreach ($sheet['widths'] as $column => $width) {
            $index = $this->columnNumber($column);
            $columns .= '<col min="'.$index.'" max="'.$index.'" width="'.$width.'" customWidth="1"/>';
        }

        $merges = '';
        foreach ($sheet['merges'] as $merge) {
            $merges .= '<mergeCell ref="'.$this->xml($merge).'"/>';
        }

        $freeze = '';
        if ($sheet['freezeAt']) {
            preg_match('/([A-Z]+)(\d+)/', strtoupper($sheet['freezeAt']), $matches);
            $xSplit = max(0, $this->columnNumber($matches[1] ?? 'A') - 1);
            $ySplit = max(0, ((int) ($matches[2] ?? 1)) - 1);
            $freeze = '<pane xSplit="'.$xSplit.'" ySplit="'.$ySplit.'" topLeftCell="'.$this->xml($sheet['freezeAt']).'" activePane="bottomRight" state="frozen"/>';
        }

        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            .'<sheetViews><sheetView workbookViewId="0">'.$freeze.'</sheetView></sheetViews>'
            .($columns ? '<cols>'.$columns.'</cols>' : '')
            .'<sheetData>'.$rows.'</sheetData>'
            .($merges ? '<mergeCells count="'.count($sheet['merges']).'">'.$merges.'</mergeCells>' : '')
            .($sheet['autoFilter'] ? '<autoFilter ref="'.$this->xml($sheet['autoFilter']).'"/>' : '')
            .'<pageMargins left="0.25" right="0.25" top="0.5" bottom="0.5" header="0.2" footer="0.2"/>'
            .'<pageSetup orientation="landscape" fitToWidth="1" fitToHeight="0"/></worksheet>';
    }

    private function styles(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            .'<numFmts count="1"><numFmt numFmtId="164" formatCode="#,##0.00;[Red]-#,##0.00"/></numFmts>'
            .'<fonts count="4"><font><sz val="11"/><name val="Montserrat"/></font><font><b/><color rgb="FFFFFFFF"/><sz val="11"/><name val="Montserrat"/></font><font><b/><color rgb="FFFFFFFF"/><sz val="16"/><name val="Montserrat"/></font><font><b/><sz val="11"/><name val="Montserrat"/></font></fonts>'
            .'<fills count="7"><fill><patternFill patternType="none"/></fill><fill><patternFill patternType="gray125"/></fill><fill><patternFill patternType="solid"><fgColor rgb="FF2D5A27"/><bgColor indexed="64"/></patternFill></fill><fill><patternFill patternType="solid"><fgColor rgb="FF11BFE3"/><bgColor indexed="64"/></patternFill></fill><fill><patternFill patternType="solid"><fgColor rgb="FFC9A962"/><bgColor indexed="64"/></patternFill></fill><fill><patternFill patternType="solid"><fgColor rgb="FFF3F4F6"/><bgColor indexed="64"/></patternFill></fill><fill><patternFill patternType="solid"><fgColor rgb="FFFEE2E2"/><bgColor indexed="64"/></patternFill></fill></fills>'
            .'<borders count="2"><border><left/><right/><top/><bottom/><diagonal/></border><border><left style="thin"><color rgb="FFD1D5DB"/></left><right style="thin"><color rgb="FFD1D5DB"/></right><top style="thin"><color rgb="FFD1D5DB"/></top><bottom style="thin"><color rgb="FFD1D5DB"/></bottom><diagonal/></border></borders>'
            .'<cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs>'
            .'<cellXfs count="12">'
            .'<xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/>'
            .'<xf numFmtId="0" fontId="2" fillId="2" borderId="0" xfId="0" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>'
            .'<xf numFmtId="0" fontId="3" fillId="0" borderId="0" xfId="0"/>'
            .'<xf numFmtId="0" fontId="1" fillId="3" borderId="1" xfId="0" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>'
            .'<xf numFmtId="0" fontId="1" fillId="2" borderId="1" xfId="0" applyAlignment="1"><alignment horizontal="center" vertical="center" wrapText="1"/></xf>'
            .'<xf numFmtId="0" fontId="0" fillId="0" borderId="1" xfId="0" applyAlignment="1"><alignment vertical="center"/></xf>'
            .'<xf numFmtId="164" fontId="0" fillId="0" borderId="1" xfId="0" applyNumberFormat="1"/>'
            .'<xf numFmtId="164" fontId="3" fillId="4" borderId="1" xfId="0" applyNumberFormat="1"/>'
            .'<xf numFmtId="0" fontId="3" fillId="4" borderId="1" xfId="0"/>'
            .'<xf numFmtId="0" fontId="0" fillId="5" borderId="1" xfId="0"/>'
            .'<xf numFmtId="164" fontId="0" fillId="5" borderId="1" xfId="0" applyNumberFormat="1"/>'
            .'<xf numFmtId="164" fontId="0" fillId="6" borderId="1" xfId="0" applyNumberFormat="1"/>'
            .'</cellXfs><cellStyles count="1"><cellStyle name="Normal" xfId="0" builtinId="0"/></cellStyles></styleSheet>';
    }

    private function appProperties(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><Properties xmlns="http://schemas.openxmlformats.org/officeDocument/2006/extended-properties" xmlns:vt="http://schemas.openxmlformats.org/officeDocument/2006/docPropsVTypes"><Application>Villa-Sina</Application></Properties>';
    }

    private function coreProperties(): string
    {
        $now = gmdate('Y-m-d\TH:i:s\Z');

        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><cp:coreProperties xmlns:cp="http://schemas.openxmlformats.org/package/2006/metadata/core-properties" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:dcterms="http://purl.org/dc/terms/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"><dc:creator>Villa-Sina</dc:creator><dc:title>Laporan Keuangan</dc:title><dcterms:created xsi:type="dcterms:W3CDTF">'.$now.'</dcterms:created></cp:coreProperties>';
    }

    private function safeSheetName(string $name): string
    {
        return mb_substr(preg_replace('/[\\\\\/\?\*\[\]:]/', '-', $name) ?: 'Sheet', 0, 31);
    }

    private function columnNumber(string $column): int
    {
        $number = 0;
        foreach (str_split(strtoupper($column)) as $letter) {
            $number = ($number * 26) + ord($letter) - 64;
        }

        return $number;
    }

    private function xml(string $value): string
    {
        return htmlspecialchars($value, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    }
}
