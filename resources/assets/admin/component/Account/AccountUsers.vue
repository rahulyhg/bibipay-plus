<template>
        <div class="container-fluid" style="margin-top:10px;">
        <div class="panel panel-default" style="overflow: hidden;">
            <div class="col-sm-5">
                <label for="search-input-name" class="col-sm-6 control-label">共注册：</label>
                <div class="col-sm-6">
                {{data.login_count}} 人
                </div>
            </div>
            <div class="col-sm-5">
                <label for="search-input-name" class="col-sm-6 control-label">当日注册：</label>
                <div class="col-sm-6">
                {{data.today_login_count}} 人
                </div>
            </div>
            <div class="col-sm-5">
                <label for="search-input-name" class="col-sm-6 control-label">共充值（usdt）：</label>
                <div class="col-sm-6">
                {{data.usdt_count}}
                </div>
            </div>
            <div class="col-sm-5">
                <label for="search-input-name" class="col-sm-6 control-label">当日充值（usdt）：</label>
                <div class="col-sm-6">
                {{data.today_usdt_count}}
                </div>
            </div>
            <div class="col-sm-2">
                <a @click="$router.push('/account/users/deteail')">
                    充值查询
                </a>
            </div>
        </div>
            <div class="col-sm-12 panel" style="margin-bottom: 0px;background-color: rgba(0,0,0,0);border:none;box-shadow:none;">
                <div class="col-sm-1">
                    时间：
                </div>
                <div class="col-sm-6">
                     <input type="date" class="col-sm-3" v-model="regist_start_date">
                        <div class="input-group-addon col-sm-1">-</div>
                     <input type="date" class="col-sm-3" v-model="regist_end_date">
                </div>
            </div>
            <div class="col-sm-12 panel" style="margin-bottom: 0px;background-color: rgba(0,0,0,0);border:none;box-shadow:none;">
                 <div class="col-sm-1">
                     搜索：
                 </div>
                 <div class="col-sm-2">
                     <input v-model="userId" placeholder="id">
                 </div>
                 <div class="col-sm-2">
                     <input v-model="phone" placeholder="手机号码">
                 </div>
                 <div class="col-sm-2">
                     <input v-model="name" placeholder="用户名">
                 </div>
                 <div class="col-sm-2" style="float: right;">
                     <button class="btn btn-default" @click="del()">
                       清空
                     </button>
                     <button class="btn btn-primary" @click="getAdSpaces()">
                         搜索
                     </button>
                 </div>
            </div>
            <div class="panel-body">
              <table class="table table-bordered">
                <thead>
                  <tr>
                    <th>#ID</th>
                    <th>用户名称</th>
                    <th>手机号</th>
                    <th>注册时间</th>
                  </tr>
                </thead>
                <tbody>
                  <!-- <table-loading :loadding="loading" :colspan-num="10"></table-loading> -->
                  <tr v-for="item in data.data.data">
                      <td>{{item.id}}</td>
                      <td>{{item.name}}</td>
                      <td>{{item.tel}}</td>
                      <td>{{item.created_at}}</td>
                  </tr>
                </tbody>
              </table>
              <!-- 分页 -->
                <el-pagination
                  background
                  :page-size="15"
                  @current-change="handleCurrentChange"
                  layout="prev, pager, next"
                  :total="total">
                </el-pagination>
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

const AccountUesrs = {
  /**
   * 定义当前组件状态数据
   *
   * @return {Object}
   * @author Seven Du <shiweidu@outlook.com>
   * @homepage http://medz.cn
   */
     data() {
        return {
          data:{},
          name:"",
          userId:"",
          phone:"",
          regist_start_date:"",
          regist_end_date:"",
          total:0,
          page:0
        }
      },
   mounted (){
     this.getAdSpaces();
   },
   methods: {
     del(){
        this.name = this.userId = this.phone = this.regist_start_date = this.regist_end_date = "";
     },
     onDetails (id){
        this.$router.push('/Details/' + id)
     },
     getAdSpaces () {
       request.post(
         createRequestURI('qz/user_list'),
         {
            id:this.userId,
            name:this.name,
            tel:this.phone,
            beginTime:this.regist_start_date,
            endTime:this.regist_end_date,
            page: this.page
          },
         {validateStatus: status => status === 200})
           .then(response => {
           this.data = response.data;
           this.total = response.data.data.total;
           console.log(this.data)
       }).catch(({ response: { data: { errors = ['加载认证类型失败'] } = {} } = {} }) => {
       });
     },
     handleCurrentChange(val){
        this.page = val;
        this.getAdSpaces();
     }
 }
};
export default AccountUesrs;
</script>
