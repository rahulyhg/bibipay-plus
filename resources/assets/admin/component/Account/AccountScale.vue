<template>
<div>
    <div class="panel-heading">
        检索用户
        <router-link tag="a" class="btn btn-link pull-right btn-xs" to="/users/add" role="button">
            <span class="glyphicon glyphicon-plus"></span>
            添加
        </router-link>
        <span class="pull-right">&nbsp;\&nbsp;</span>
        <router-link tag="a" class="btn btn-link pull-right btn-xs" to="/users/trashed" role="button">
            <span class="glyphicon glyphicon-warning-sign"></span>
            停用管理
        </router-link>
    </div>
    <div class="panel-heading">
        <!-- 搜索用户 -->
        <div class="form-horizontal">
            <div class="form-group">
                <label class="col-sm-2 control-label">USDT:IPC 等于</label>
                <div class="col-sm-4">
                    <input  type="text" class="form-control" id="search-input-email" placeholder="请输入搜索邮箱地址，支持模糊搜索">
                </div>
                <div class="col-sm-4">
                    <input  type="text" class="form-control" id="search-input-email" placeholder="请输入搜索邮箱地址，支持模糊搜索">
                </div>
            </div>
        </div>
    </div>
</div>
</template>
<style scoped>
.dropbtn {
  border: none;
  cursor: pointer;
}
.dropdown {
  position: relative;
  display: inline-block;
}
.dropdown-content {
  display: none;
  position: absolute;
  background-color: #f9f9f9;
  min-width: 40px;
  box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);
}
.dropdown-content a {
  color: black;
  padding: 10px;
  text-decoration: none;
  display: block;
}
.dropdown-content a:hover {
  background-color: #f1f1f1;
}
.dropdown:hover .dropdown-content {
  display: block;
}
</style>


<script>
import lodash from "lodash";
import { mapGetters } from "vuex";
import request, { createRequestURI } from "../../util/request";
// import AreaLinkage from './AreaLinkage';

const AccountScale = {
  /**
   * 定义当前组件状态数据
   *
   * @return {Object}
   * @author Seven Du <shiweidu@outlook.com>
   * @homepage http://medz.cn
   */
};
export default AccountScale;
</script>
