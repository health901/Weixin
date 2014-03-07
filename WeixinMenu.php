<?php

/**
 * 微信自定义菜单辅助类
 * 
 * @author Viking Robin <admin@vkrobin.com>
 */
Class WeixinMenu {

	private $buttons = array();

	/**
	 * 创建一级菜单
	 * 
	 * @param string $name 菜单标题，不超过16个字节，子菜单不超过40个字节
	 * @param string $type 菜单的响应动作类型，目前有click、view两种类型 
	 * @param string $value  若为click类型,则为key的值，若为view类型，则为url链接
	 * @return boolean 返回是否创建成功
	 */
	public function addButton($name, $type = NULL, $value = NULL) {
		if (sizeof($this->buttons) == 3) {
			return FALSE;
		}
		if ($type == 'view') {
			$button['type'] = 'view';
			$button['url'] = $value;
		} else {
			$button['type'] = 'click';
			$button['key'] = $value;
		}
		$button['name'] = $name;
		$this->buttons[] = $button;
	}

	/**
	 * 创建二级菜单
	 * 
	 * @param int $index 父按钮序号 1~3 之间
	 * @param string $name 菜单标题，不超过16个字节，子菜单不超过40个字节
	 * @param string $type 菜单的响应动作类型，目前有click、view两种类型 
	 * @param string $value  若为click类型,则为key的值，若为view类型，则为url链接
	 * @return boolean 返回是否创建成功
	 */
	public function addSubButton($index, $name, $type, $value) {
		$index--;
		if (!isset($this->buttons[$index])) {
			return FALSE;
		}
		if (sizeof($this->buttons[$index]) == 5) {
			return FALSE;
		}
		if ($type == 'view') {
			$button['type'] = 'view';
			$button['url'] = $value;
		} else {
			$button['type'] = 'click';
			$button['key'] = $value;
		}
		$button['name'] = $name;
		$this->buttons[$index]['sub'][] = $button;
		return TRUE;
	}

	/**
	 * 导出数组
	 * 
	 * @return array
	 */
	public function toArray() {
		$buttons = array();
		foreach ($this->buttons as $button) {
			if (isset($button['sub'])) {
				$buttons[] = array('name' => $button['name'], 'sub_button' => $button['sub']);
			} else {
				$buttons[] = $button;
			}
		}
		return array('button' => $buttons);
	}

}

