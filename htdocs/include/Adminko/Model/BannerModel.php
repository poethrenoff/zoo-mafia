<?php
namespace Adminko\Model;

use Adminko\Db\Db;

class BannerModel extends Model
{
    // Получение случайного баннера
    public function getBanner()
    {
        $banner = Db::selectRow('
            select * from banner where banner_active = :banner_active order by rand() limit 1', array('banner_active' => 1));
        return $this->get($banner['banner_id'], $banner);
    }
}
