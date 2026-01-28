<?php
// +----------------------------------------------------------------------
// | Success, real success,
// | is being willing to do the things that other people are not.
// +----------------------------------------------------------------------
// | Author:    ValencioKang <ailin1219@foxmail.com>
// +----------------------------------------------------------------------
// | FileName:  ExportManager.php
// +----------------------------------------------------------------------
// | Year:      2025/8/14/八月
// +----------------------------------------------------------------------
declare (strict_types=1);

namespace Valencio\LaravelKit\Export;

use Illuminate\Contracts\Foundation\Application;
use Vtiful\Kernel\Excel;
use Vtiful\Kernel\Format;

class ExportManager
{
    /**
     * @var Application
     */
    protected Application $app;


    /**
     * @var iterable
     */
    private iterable $data;

    /**
     * 表头
     *
     * @var array
     */
    private array $header = [];

    /**
     * 文件名
     *
     * @var string
     */
    private string $fileName = '';


    /**
     * 列宽
     *
     * @var array
     */
    private array $columnsWith = [];


    /**
     * 构造函数
     * @param Application $app
     */
    public function __construct (Application $app)
    {
        $this->app = $app;
    }


    /**
     * @param array $header
     * @param iterable $data
     * @param string $fileName
     * @param array $columnsWith
     * @param int $maxExportCount
     * @return self
     */
    public function assign (
        array    $header,
        iterable $data,
        string   $fileName = '',
        array    $columnsWith = [],
        int      $maxExportCount = 500000
    ): self {
        $this->header = $header;
        $this->data = $data;
        $this->fileName = $fileName;
        $this->columnsWith = $columnsWith;


        return $this;
    }


    /**
     * 处理数据
     *
     * @return string
     */
    public function handle (): string
    {
        $fileName = $this->setFilename();

        [$excel, $fileObject, $fileHandle] = $this->init($fileName);

        $this->setTitleStyle($fileHandle, $fileObject);

        $this->writeTableHeader($fileHandle, $fileObject);

        $this->writeTableData($fileObject);

        $this->setColumnsWidth($fileObject);

        $filePath = $fileObject->output();

        $excel->close();

        return asset('storage/exports/' . basename($filePath));
    }

    /**
     * @return string
     */
    private function setFilename (): string
    {
        $formatName = empty($this->fileName) ? 'export_' . now()->format('Ymd_His') : $this->fileName . '_' . now()->format('Ymd_His');
        $formatName .= '.xlsx';
        return $formatName;
    }


    /**
     * 初始化导出服务
     *
     * @param string $fileName
     * @return array
     */
    private function init (string $fileName): array
    {
        $path = storage_path('app/public/exports');
        if (!is_dir($path)) mkdir($path, 0777, true);

        $excel = new Excel(['path' => $path,]);

        $fileObject = $excel->constMemory($fileName);

        $fileHandle = $excel->getHandle();

        return [
            $excel,
            $fileObject,
            $fileHandle
        ];
    }


    /**
     * 设置标题样式
     *
     * @param $fileHandle
     * @param $fileObject
     * @return void
     */
    private function setTitleStyle ($fileHandle, $fileObject): void
    {
        $titleFormat = new Format($fileHandle);
        $titleStyle = $titleFormat->bold()
            ->align(Format::FORMAT_ALIGN_CENTER, Format::FORMAT_ALIGN_VERTICAL_CENTER)
            ->toResource();

        // 第一行：合并写入标题
        $fileObject->setRow('A1', 30, $titleStyle); // 行高 20
        $fileObject->mergeCells('A1:' . $this->getTotalColumnLetter() . '1', $this->fileName);
    }


    /**
     * 写入表头
     *
     * @param $fileHandle
     * @param $fileObject
     * @return void
     */
    private function writeTableHeader ($fileHandle, $fileObject): void
    {
        // 设置表头样式（第二行：加粗）
        $headerFormat = new Format($fileHandle);
        $headerStyle = $headerFormat->bold()->toResource();

        $rowFormat = new Format($fileHandle);
        $alignStyle = $rowFormat
            ->align(Format::FORMAT_ALIGN_CENTER, Format::FORMAT_ALIGN_VERTICAL_CENTER)
            ->toResource();
        $fileObject->defaultFormat($alignStyle);

        // 写入第二行表头
        $fileObject->setCurrentLine(1); // 明确设置行指针到第 2 行（A2）
        $fileObject->setRow('A2', 15, $headerStyle); // 行高 15
        $fileObject->data([$this->header]);
    }


    /**
     * 写入数据
     *
     * @param $fileObject
     * @return void
     */
    /**
     * 写入数据
     *
     * @param $fileObject
     * @return void
     */
    private function writeTableData ($fileObject): void
    {
        $currentLine = 2; // ✅ 当前写入行（从第2行开始，1行是表头）
        $fileObject->setCurrentLine($currentLine);

        $batchSize = 1000;
        $batch = [];

        foreach ($this->data as $row) {
            $batch[] = $row;

            if (count($batch) >= $batchSize) {
                // ✅ 写入这一批
                $fileObject->data($batch);

                // ✅ 关键：推进行指针，否则会覆盖前面的数据
                $currentLine += count($batch);
                $fileObject->setCurrentLine($currentLine);

                $batch = [];
            }
        }

        // 写入剩余的数据
        if (!empty($batch)) {
            $fileObject->data($batch);

            // ✅ 同样推进一下（虽然最后一次不推进也无所谓，但保持一致更稳）
            $currentLine += count($batch);
            $fileObject->setCurrentLine($currentLine);
        }
    }

    /**
     * 设置列宽
     *
     * @param $fileObject
     * @return void
     */
    private function setColumnsWidth ($fileObject): void
    {
        if (!empty($this->columnsWith)) {
            foreach ($this->columnsWith as $column => $width) {
                $fileObject->setColumn($column, $width);
            }
        }
    }


    /**
     * 获取总列数对应的字母
     *
     * @return string
     */
    public function getTotalColumnLetter (): string
    {
        return chr(count($this->header) + 64);
    }
}
