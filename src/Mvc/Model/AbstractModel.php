<?php


namespace Phramework\Mvc\Model;

use Phalcon\Mvc\Model;

class AbstractModel extends Model
{
  public function initialize()
  {
    $this->setSource($this->getTableSource());
  }

	/**
	 * @param $id
	 * @return Model|bool
	 */
	public function deleteById($id)
	{
		$model = self::findFirst($id);
    if(!$model)
    {
      return false;
    }

		$model->delete();

		return $model;
	}

	/**
	 * Returns table name mapped in the model
	 *
	 * @return string
	 */
	protected function getTableSource()
	{
		$className = get_class($this);
		$array = [];
    $pattern = addslashes("/Modules\(.*?)\Model\(.*)Model/");
    preg_match($pattern, $className, $array);

		return lcfirst(ltrim($array[2], '\\'));
	}

}
