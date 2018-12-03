<template>
    <div class="panel-heading">
    <!-- 搜索用户 -->
        <div class="form-horizontal">
            <div class="form-group" v-for="item in data">
                <label for="search-input-name" class="col-sm-2 control-label">{{item.token_name}}:</label>
                <div class="col-sm-4">
                    <input  type="text" class="form-control" id="search-input-name" v-model="item.poundage" placeholder="请输入手续费">
                </div>
                <label for="search-input-name" class="col-sm-1 control-label">{{item.token_name}}</label>
                <button class="btn btn-primary btn-sm" @click="postAdSpaces(item)">
                确定
                </button>
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

const AccountCost = {
  /**
   * 定义当前组件状态数据
   *
   * @return {Object}
   * @author Seven Du <shiweidu@outlook.com>
   * @homepage http://medz.cn
   */
   data(){
     return{
        data:{}
     }
   },
   created(){
     this.getAdSpaces()
   },
   methods:{
      postAdSpaces(item){
            request.post(
               createRequestURI('poundage'),
               {id:item.id,poundage:item.poundage,token_name:item.token_name},
               { validateStatus: status => status === 200 }
             ).then(response => {
             })
      },
      getAdSpaces () {
             request.get(
               createRequestURI('get/tokens'),
               { validateStatus: status => status === 200 }
             ).then(response => {
             this.data = response.data
             })
         },
   }
};
export default AccountCost;
</script>
