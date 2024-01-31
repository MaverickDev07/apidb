<?php

  // Header Styles
  function headerTitleStyle() {
    return [
      'font' => [
        'bold' => true,
        'size'  => 14,
        'name' => 'verdana'
      ],
      'alignment' => [
        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
      ]
    ];
  }

  function headerInfoStyle() {
    return [
      'font' => [
        'bold' => true,
        'size'  => 10,
        'name' => 'verdana'
      ],
    ];
  }

  function headerInfoDetailStyle() {
    return [
      'font' => [
        'size'  => 10,
        'name' => 'verdana'
      ],
      'alignment' => [
        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
      ]
    ];
  }

  function headerTableStyle() {
    return [
      'font' => [
        'bold'  => true,
        'size'  => 10,
        'name' => 'verdana',
        'color' => array('rgb' => 'FFFFFF')
      ],
      'fill' => array(
        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
        'startColor' => array('argb' => '070D59')
      ),
      'borders' => array(
        'outline' => array(
          'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
          'color' => array('rgb' => '000000'),
        )
      ),
      'alignment' => [
        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
      ]
    ];
  }

  function headerTableNameStyle() {
    return [
      'font' => [
        'size'  => 10,
        'name' => 'verdana',
        'color' => array('rgb' => '000000')
      ],
      'fill' => array(
        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
        'startColor' => array('rgb' => 'F9F9F9')
      ),
      'borders' => array(
        'outline' => array(
          'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
          'color' => array('rgb' => '000000'),
        )
      )
    ];
  }
  function headerTableCampoStyle() {
    return [
      'font' => [
        'bold'  => true,
        'size'  => 10,
        'name' => 'verdana',
        'color' => array('rgb' => 'FFFFFF')
      ],
      'fill' => array(
        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
        'startColor' => array('rgb' => '1F3C88')
      ),
      'borders' => array(
        'outline' => array(
          'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
          'color' => array('rgb' => '000000'),
        )
      ),
      'alignment' => [
        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
      ]
    ];
  }
  function headerTableMateriaStyle() {
    return [
      'font' => [
        'bold'  => true,
        'size'  => 10,
        'name' => 'verdana',
        'color' => array('rgb' => 'FFFFFF')
      ],
      'fill' => array(
        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
        'startColor' => array('rgb' => '5893D4')
      ),
      'borders' => array(
        'outline' => array(
          'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
          'color' => array('rgb' => '000000'),
        )
      )
    ];
  }
  function headerTablePromedioStyle() {
    return [
      'font' => [
        'bold'  => true,
        'size'  => 10,
        'name' => 'verdana',
        'color' => array('rgb' => 'FFFFFF')
      ],
      'fill' => array(
        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
        'startColor' => array('rgb' => '1F3C88')
      ),
      'borders' => array(
        'outline' => array(
          'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
          'color' => array('rgb' => '000000'),
        )
      )
    ];
  }
  function headerTablePromedioCamposStyle() {
    return [
      'font' => [
        'bold'  => true,
        'size'  => 10,
        'name' => 'verdana',
        'color' => array('rgb' => 'FFFFFF')
      ],
      'fill' => array(
        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
        'startColor' => array('rgb' => '00B050')
      ),
      'borders' => array(
        'outline' => array(
          'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
          'color' => array('rgb' => '000000'),
        )
      )
    ];
  }
  function headerTableAreaStyle() {
    return [
      'font' => [
        'bold'  => true,
        'size'  => 10,
        'name' => 'verdana',
        'color' => array('rgb' => 'FFFFFF')
      ],
      'fill' => array(
        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
        'startColor' => array('rgb' => '1F3C88')
      ),
      'borders' => array(
        'outline' => array(
          'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
          'color' => array('rgb' => '000000'),
        )
      ),
      'alignment' => [
        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
      ]
    ];
  }

  function notaPromedioStyle() {
    return [
      'font' => [
        'bold' => true,
        'size'  => 10,
        'name' => 'verdana',
        'color' => array('rgb' => '000000')
      ],
      'fill' => array(
        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
        'startColor' => array('rgb' => 'CEDDEF')
      ),
      'borders' => array(
        'outline' => array(
          'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
          'color' => array('rgb' => '000000'),
        )
      )
    ];
  }
  function notaPromedioCamposStyle() {
    return [
      'font' => [
        'bold' => true,
        'size'  => 10,
        'name' => 'verdana',
        'color' => array('rgb' => '000000')
      ],
      'fill' => array(
        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
        'startColor' => array('rgb' => 'DDE8CB')
      ),
      'borders' => array(
        'outline' => array(
          'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
          'color' => array('rgb' => '000000'),
        )
      )
    ];
  }

  function notaStyle() {
    return [
      'font' => [
        'size'  => 10,
        'name' => 'verdana',
        'color' => array('rgb' => '000000')
      ],
      'borders' => array(
        'outline' => array(
          'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
          'color' => array('rgb' => '000000'),
        )
      )
    ];
  }

  function basicStyle() {
    return [
      'font' => [
        'size'  => 10,
        'name' => 'verdana',
        'color' => array('rgb' => '000000')
      ],
      'borders' => array(
        'outline' => array(
          'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
          'color' => array('rgb' => '000000'),
        )
      ),
      'alignment' => [
        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
      ]
    ];
  }

?>