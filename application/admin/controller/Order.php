<?php

namespace app\admin\controller;

use think\Controller;
use think\Db;
use think\Request;
use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class Order extends Common
{
    public function show(){
        return $this->fetch();
    }
    public function upper(){
        $file = request()->file('aaa');  //从客户端接值
        //存入服务端
        $info = $file->validate(['size'=>51024,'ext'=>'xls'])->move( 'file');
        //获取存入的日期目录以及文件名
        $luj=$info->getSaveName();

        $helper = new Sample();  //实例化 Sample
        $inputFileName = './file/'.$luj;  //拼接服务器文件路径
        $helper->log('Loading file ' . pathinfo($inputFileName, PATHINFO_BASENAME) . ' using IOFactory to identify the format');
        $spreadsheet = IOFactory::load($inputFileName);
        $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
        foreach ($sheetData as $k=>$v){
            $data = ['id' => $v['A'], 'name' => $v['B']];
            $add=Db::name('ceb')->insert($data);
            if ($add==true){
                echo "添加成功";
            }else{
                echo "添加失败";
            }
        }


    }
    public function lower()
    {
        $arr=   Db::query("select * from attr_details");

        $helper = new Sample();
        if ($helper->isCli()) {
            $helper->log('This example should only be run from a Web Browser' . PHP_EOL);

            return;
        }

// Create new Spreadsheet object
        $spreadsheet = new Spreadsheet();

// Set document properties
        $spreadsheet->getProperties()->setCreator('Maarten Balliauw')
            ->setLastModifiedBy('Maarten Balliauw')
            ->setTitle('Office 2007 XLSX Test Document')
            ->setSubject('Office 2007 XLSX Test Document')
            ->setDescription('Test document for Office 2007 XLSX, generated using PHP classes.')
            ->setKeywords('office 2007 openxml php')
            ->setCategory('Test result file');

        // Add some data
        $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue('A1', 'id')
            ->setCellValue('B1', 'name');


        foreach ($arr as $k=>$v){
            $i=$k+2;
            // Add some data
            $spreadsheet->setActiveSheetIndex(0)
                ->setCellValue('A'.$i, $v['id'])
                ->setCellValue('B'.$i, $v['name']);
        }



// Rename worksheet
        $spreadsheet->getActiveSheet()->setTitle('Simple');

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $spreadsheet->setActiveSheetIndex(0);

// Redirect output to a client’s web browser (Xls)
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="01simple.xls"');
        header('Cache-Control: max-age=0');
// If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

// If you're serving to IE over SSL, then the following may be needed
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0

        $writer = IOFactory::createWriter($spreadsheet, 'Xls');
        $writer->save('php://output');
        exit;

    }


}
