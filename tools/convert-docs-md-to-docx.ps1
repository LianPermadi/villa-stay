param(
    [string]$DocsDir = "docs"
)

$ErrorActionPreference = "Stop"

function Escape-Xml([string]$value) {
    if ($null -eq $value) { return "" }
    return [System.Security.SecurityElement]::Escape($value)
}

function Sanitize-Text([string]$value) {
    if ($null -eq $value) { return "" }
    return ($value -replace "[\x00-\x08\x0B\x0C\x0E-\x1F]", "")
}

function New-Run([string]$text, [switch]$Bold, [switch]$Italic, [switch]$Code) {
    $text = Escape-Xml (Sanitize-Text $text)
    $rPr = ""
    if ($Bold) { $rPr += "<w:b/>" }
    if ($Italic) { $rPr += "<w:i/>" }
    if ($Code) {
        $rPr += "<w:rFonts w:ascii=`"Consolas`" w:hAnsi=`"Consolas`"/><w:sz w:val=`"20`"/><w:color w:val=`"334155`"/>"
    }
    if ($rPr) { $rPr = "<w:rPr>$rPr</w:rPr>" }
    return "<w:r>$rPr<w:t xml:space=`"preserve`">$text</w:t></w:r>"
}

function New-Paragraph([string]$text, [string]$style = "Normal", [int]$numId = 0, [int]$ilvl = 0, [switch]$Code) {
    $pPr = ""
    if ($style -and $style -ne "Normal") {
        $pPr += "<w:pStyle w:val=`"$style`"/>"
    }
    if ($numId -gt 0) {
        $pPr += "<w:numPr><w:ilvl w:val=`"$ilvl`"/><w:numId w:val=`"$numId`"/></w:numPr>"
    }
    if ($Code) {
        $pPr += "<w:spacing w:before=`"0`" w:after=`"0`"/><w:shd w:val=`"clear`" w:color=`"auto`" w:fill=`"F1F5F9`"/>"
    }
    if ($pPr) { $pPr = "<w:pPr>$pPr</w:pPr>" }
    return "<w:p>$pPr$(New-Run -text $text -Code:$Code)</w:p>"
}

function Split-MarkdownTableRow([string]$line) {
    $trimmed = $line.Trim()
    if ($trimmed.StartsWith("|")) { $trimmed = $trimmed.Substring(1) }
    if ($trimmed.EndsWith("|")) { $trimmed = $trimmed.Substring(0, $trimmed.Length - 1) }
    return @($trimmed -split "\|" | ForEach-Object { $_.Trim() })
}

function Is-TableSeparator([string]$line) {
    return $line.Trim() -match "^\|?\s*:?-{3,}:?\s*(\|\s*:?-{3,}:?\s*)+\|?\s*$"
}

function New-TableXml([array]$rows) {
    if ($rows.Count -eq 0) { return "" }
    $maxCols = 1
    foreach ($row in $rows) {
        if ($row.Count -gt $maxCols) { $maxCols = $row.Count }
    }
    $tableWidth = 9360
    $cellWidth = [Math]::Floor($tableWidth / $maxCols)
    $grid = ""
    for ($i = 0; $i -lt $maxCols; $i++) {
        $grid += "<w:gridCol w:w=`"$cellWidth`"/>"
    }
    $xml = "<w:tbl><w:tblPr><w:tblStyle w:val=`"TableGrid`"/><w:tblW w:w=`"$tableWidth`" w:type=`"dxa`"/><w:tblLayout w:type=`"fixed`"/><w:tblCellMar><w:top w:w=`"100`" w:type=`"dxa`"/><w:left w:w=`"120`" w:type=`"dxa`"/><w:bottom w:w=`"100`" w:type=`"dxa`"/><w:right w:w=`"120`" w:type=`"dxa`"/></w:tblCellMar></w:tblPr><w:tblGrid>$grid</w:tblGrid>"
    for ($r = 0; $r -lt $rows.Count; $r++) {
        $xml += "<w:tr>"
        for ($c = 0; $c -lt $maxCols; $c++) {
            $cell = ""
            if ($c -lt $rows[$r].Count) { $cell = $rows[$r][$c] }
            $fill = ""
            if ($r -eq 0) {
                $fill = "<w:shd w:val=`"clear`" w:color=`"auto`" w:fill=`"E2E8F0`"/>"
            }
            $bold = $r -eq 0
            $xml += "<w:tc><w:tcPr><w:tcW w:w=`"$cellWidth`" w:type=`"dxa`"/>$fill</w:tcPr><w:p><w:pPr><w:spacing w:after=`"60`"/></w:pPr>$(New-Run -text $cell -Bold:$bold)</w:p></w:tc>"
        }
        $xml += "</w:tr>"
    }
    $xml += "</w:tbl>"
    return $xml
}

function Convert-MarkdownToDocumentXml([string[]]$lines, [string]$title) {
    $body = New-Object System.Collections.Generic.List[string]
    $body.Add((New-Paragraph -text $title -style "Title"))
    $body.Add((New-Paragraph -text ("Dikonversi dari Markdown pada " + (Get-Date -Format "yyyy-MM-dd HH:mm")) -style "Subtitle"))

    $inCode = $false
    $codeBuffer = New-Object System.Collections.Generic.List[string]
    $i = 0

    while ($i -lt $lines.Count) {
        $line = $lines[$i]
        $trim = $line.Trim()

        if ($trim -match '^```') {
            if ($inCode) {
                foreach ($codeLine in $codeBuffer) {
                    $body.Add((New-Paragraph -text $codeLine -Code))
                }
                $body.Add((New-Paragraph -text ""))
                $codeBuffer.Clear()
                $inCode = $false
            } else {
                $inCode = $true
            }
            $i++
            continue
        }

        if ($inCode) {
            $codeBuffer.Add($line)
            $i++
            continue
        }

        if ([string]::IsNullOrWhiteSpace($line)) {
            $i++
            continue
        }

        if ($trim -match '^!\[(.*?)\]\((.*?)\)') {
            $body.Add((New-Paragraph -text ("Gambar: " + $Matches[1] + " (" + $Matches[2] + ")") -style "Caption"))
            $i++
            continue
        }

        if ($trim -match '^(#{1,6})\s+(.*)$') {
            $level = $Matches[1].Length
            $text = $Matches[2]
            if ($level -eq 1) { $style = "Heading1" }
            elseif ($level -eq 2) { $style = "Heading2" }
            else { $style = "Heading3" }
            $body.Add((New-Paragraph -text $text -style $style))
            $i++
            continue
        }

        if ($trim -match '^\|.*\|$' -and ($i + 1) -lt $lines.Count -and (Is-TableSeparator $lines[$i + 1])) {
            $rows = New-Object System.Collections.Generic.List[object]
            $rows.Add((Split-MarkdownTableRow $line))
            $i += 2
            while ($i -lt $lines.Count -and $lines[$i].Trim() -match '^\|.*\|$') {
                $rows.Add((Split-MarkdownTableRow $lines[$i]))
                $i++
            }
            $body.Add((New-TableXml $rows.ToArray()))
            $body.Add((New-Paragraph -text ""))
            continue
        }

        if ($trim -match '^[-*+]\s+(.*)$') {
            $body.Add((New-Paragraph -text $Matches[1] -numId 1))
            $i++
            continue
        }

        if ($trim -match '^\d+\.\s+(.*)$') {
            $body.Add((New-Paragraph -text $Matches[1] -numId 2))
            $i++
            continue
        }

        $body.Add((New-Paragraph -text $trim))
        $i++
    }

    $sectPr = "<w:sectPr><w:pgSz w:w=`"12240`" w:h=`"15840`"/><w:pgMar w:top=`"1440`" w:right=`"1440`" w:bottom=`"1440`" w:left=`"1440`" w:header=`"720`" w:footer=`"720`" w:gutter=`"0`"/></w:sectPr>"
    $content = ($body -join "`n") + $sectPr
    return "<?xml version=`"1.0`" encoding=`"UTF-8`" standalone=`"yes`"?><w:document xmlns:w=`"http://schemas.openxmlformats.org/wordprocessingml/2006/main`" xmlns:r=`"http://schemas.openxmlformats.org/officeDocument/2006/relationships`"><w:body>$content</w:body></w:document>"
}

function Write-DocxPackage([string]$outPath, [string]$documentXml, [string]$title) {
    $tempRoot = Join-Path ([System.IO.Path]::GetTempPath()) ("docxbuild_" + [guid]::NewGuid().ToString("N"))
    New-Item -ItemType Directory -Path $tempRoot | Out-Null
    New-Item -ItemType Directory -Path (Join-Path $tempRoot "_rels") | Out-Null
    New-Item -ItemType Directory -Path (Join-Path $tempRoot "docProps") | Out-Null
    New-Item -ItemType Directory -Path (Join-Path $tempRoot "word") | Out-Null
    New-Item -ItemType Directory -Path (Join-Path $tempRoot "word/_rels") | Out-Null

    $contentTypes = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types"><Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/><Default Extension="xml" ContentType="application/xml"/><Override PartName="/word/document.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.document.main+xml"/><Override PartName="/word/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.styles+xml"/><Override PartName="/word/numbering.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.numbering+xml"/><Override PartName="/docProps/core.xml" ContentType="application/vnd.openxmlformats-package.core-properties+xml"/><Override PartName="/docProps/app.xml" ContentType="application/vnd.openxmlformats-officedocument.extended-properties+xml"/></Types>'
    $rels = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="word/document.xml"/><Relationship Id="rId2" Type="http://schemas.openxmlformats.org/package/2006/relationships/metadata/core-properties" Target="docProps/core.xml"/><Relationship Id="rId3" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/extended-properties" Target="docProps/app.xml"/></Relationships>'
    $docRels = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/><Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/numbering" Target="numbering.xml"/></Relationships>'
    $styles = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><w:styles xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main"><w:style w:type="paragraph" w:default="1" w:styleId="Normal"><w:name w:val="Normal"/><w:qFormat/><w:pPr><w:spacing w:after="120" w:line="276" w:lineRule="auto"/></w:pPr><w:rPr><w:rFonts w:ascii="Arial" w:hAnsi="Arial"/><w:sz w:val="22"/></w:rPr></w:style><w:style w:type="paragraph" w:styleId="Title"><w:name w:val="Title"/><w:basedOn w:val="Normal"/><w:qFormat/><w:pPr><w:spacing w:after="240"/></w:pPr><w:rPr><w:b/><w:color w:val="0F172A"/><w:sz w:val="44"/></w:rPr></w:style><w:style w:type="paragraph" w:styleId="Subtitle"><w:name w:val="Subtitle"/><w:basedOn w:val="Normal"/><w:qFormat/><w:rPr><w:color w:val="64748B"/><w:sz w:val="22"/></w:rPr></w:style><w:style w:type="paragraph" w:styleId="Heading1"><w:name w:val="heading 1"/><w:basedOn w:val="Normal"/><w:next w:val="Normal"/><w:qFormat/><w:pPr><w:keepNext/><w:spacing w:before="360" w:after="160"/><w:outlineLvl w:val="0"/></w:pPr><w:rPr><w:b/><w:color w:val="0F172A"/><w:sz w:val="32"/></w:rPr></w:style><w:style w:type="paragraph" w:styleId="Heading2"><w:name w:val="heading 2"/><w:basedOn w:val="Normal"/><w:next w:val="Normal"/><w:qFormat/><w:pPr><w:keepNext/><w:spacing w:before="280" w:after="120"/><w:outlineLvl w:val="1"/></w:pPr><w:rPr><w:b/><w:color w:val="1E3A8A"/><w:sz w:val="28"/></w:rPr></w:style><w:style w:type="paragraph" w:styleId="Heading3"><w:name w:val="heading 3"/><w:basedOn w:val="Normal"/><w:next w:val="Normal"/><w:qFormat/><w:pPr><w:keepNext/><w:spacing w:before="220" w:after="100"/><w:outlineLvl w:val="2"/></w:pPr><w:rPr><w:b/><w:color w:val="334155"/><w:sz w:val="24"/></w:rPr></w:style><w:style w:type="paragraph" w:styleId="Caption"><w:name w:val="Caption"/><w:basedOn w:val="Normal"/><w:qFormat/><w:rPr><w:i/><w:color w:val="475569"/><w:sz w:val="20"/></w:rPr></w:style><w:style w:type="table" w:styleId="TableGrid"><w:name w:val="Table Grid"/><w:basedOn w:val="TableNormal"/><w:uiPriority w:val="39"/><w:qFormat/><w:tblPr><w:tblBorders><w:top w:val="single" w:sz="4" w:space="0" w:color="CBD5E1"/><w:left w:val="single" w:sz="4" w:space="0" w:color="CBD5E1"/><w:bottom w:val="single" w:sz="4" w:space="0" w:color="CBD5E1"/><w:right w:val="single" w:sz="4" w:space="0" w:color="CBD5E1"/><w:insideH w:val="single" w:sz="4" w:space="0" w:color="CBD5E1"/><w:insideV w:val="single" w:sz="4" w:space="0" w:color="CBD5E1"/></w:tblBorders></w:tblPr></w:style></w:styles>'
    $numbering = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><w:numbering xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main"><w:abstractNum w:abstractNumId="1"><w:multiLevelType w:val="hybridMultilevel"/><w:lvl w:ilvl="0"><w:start w:val="1"/><w:numFmt w:val="bullet"/><w:lvlText w:val="•"/><w:lvlJc w:val="left"/><w:pPr><w:ind w:left="720" w:hanging="360"/></w:pPr></w:lvl></w:abstractNum><w:abstractNum w:abstractNumId="2"><w:multiLevelType w:val="hybridMultilevel"/><w:lvl w:ilvl="0"><w:start w:val="1"/><w:numFmt w:val="decimal"/><w:lvlText w:val="%1."/><w:lvlJc w:val="left"/><w:pPr><w:ind w:left="720" w:hanging="360"/></w:pPr></w:lvl></w:abstractNum><w:num w:numId="1"><w:abstractNumId w:val="1"/></w:num><w:num w:numId="2"><w:abstractNumId w:val="2"/></w:num></w:numbering>'
    $now = (Get-Date).ToUniversalTime().ToString("yyyy-MM-ddTHH:mm:ssZ")
    $core = "<?xml version=`"1.0`" encoding=`"UTF-8`" standalone=`"yes`"?><cp:coreProperties xmlns:cp=`"http://schemas.openxmlformats.org/package/2006/metadata/core-properties`" xmlns:dc=`"http://purl.org/dc/elements/1.1/`" xmlns:dcterms=`"http://purl.org/dc/terms/`" xmlns:dcmitype=`"http://purl.org/dc/dcmitype/`" xmlns:xsi=`"http://www.w3.org/2001/XMLSchema-instance`"><dc:title>$(Escape-Xml $title)</dc:title><dc:creator>Codex</dc:creator><cp:lastModifiedBy>Codex</cp:lastModifiedBy><dcterms:created xsi:type=`"dcterms:W3CDTF`">$now</dcterms:created><dcterms:modified xsi:type=`"dcterms:W3CDTF`">$now</dcterms:modified></cp:coreProperties>"
    $app = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><Properties xmlns="http://schemas.openxmlformats.org/officeDocument/2006/extended-properties" xmlns:vt="http://schemas.openxmlformats.org/officeDocument/2006/docPropsVTypes"><Application>Codex</Application></Properties>'

    Set-Content -LiteralPath (Join-Path $tempRoot "[Content_Types].xml") -Value $contentTypes -Encoding UTF8
    Set-Content -LiteralPath (Join-Path $tempRoot "_rels/.rels") -Value $rels -Encoding UTF8
    Set-Content -LiteralPath (Join-Path $tempRoot "word/_rels/document.xml.rels") -Value $docRels -Encoding UTF8
    Set-Content -LiteralPath (Join-Path $tempRoot "word/document.xml") -Value $documentXml -Encoding UTF8
    Set-Content -LiteralPath (Join-Path $tempRoot "word/styles.xml") -Value $styles -Encoding UTF8
    Set-Content -LiteralPath (Join-Path $tempRoot "word/numbering.xml") -Value $numbering -Encoding UTF8
    Set-Content -LiteralPath (Join-Path $tempRoot "docProps/core.xml") -Value $core -Encoding UTF8
    Set-Content -LiteralPath (Join-Path $tempRoot "docProps/app.xml") -Value $app -Encoding UTF8

    Add-Type -AssemblyName System.IO.Compression
    Add-Type -AssemblyName System.IO.Compression.FileSystem
    if (Test-Path $outPath) { Remove-Item -LiteralPath $outPath -Force }
    $zip = [System.IO.Compression.ZipFile]::Open($outPath, [System.IO.Compression.ZipArchiveMode]::Create)
    try {
        $files = Get-ChildItem -LiteralPath $tempRoot -Recurse -File
        foreach ($file in $files) {
            $relative = $file.FullName.Substring($tempRoot.Length).TrimStart('\', '/') -replace '\\', '/'
            [System.IO.Compression.ZipFileExtensions]::CreateEntryFromFile($zip, $file.FullName, $relative) | Out-Null
        }
    } finally {
        $zip.Dispose()
    }
    Remove-Item -LiteralPath $tempRoot -Recurse -Force
}

$resolvedDocs = Resolve-Path $DocsDir
$mdFiles = Get-ChildItem -LiteralPath $resolvedDocs -Filter "*.md" -File
foreach ($md in $mdFiles) {
    $lines = Get-Content -LiteralPath $md.FullName
    $titleLine = $lines | Where-Object { $_ -match "^#\s+" } | Select-Object -First 1
    $title = $md.BaseName
    if ($titleLine) {
        $title = ($titleLine -replace "^#\s+", "").Trim()
    }
    $xml = Convert-MarkdownToDocumentXml -lines $lines -title $title
    $out = Join-Path $md.DirectoryName ($md.BaseName + ".docx")
    Write-DocxPackage -outPath $out -documentXml $xml -title $title
    Write-Host "Created $out"
}
