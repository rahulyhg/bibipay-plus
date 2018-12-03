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
                <a  @click="$router.go(-1)">
                    返回
                </a>
            </div>
        </div>
            <div class="panel-body">
                <div class="col-sm-12" >
                    搜索：
                    <input type="text" v-model="userName" placeholder="用户名称 ">
                    <button class="btn btn-primary btn-sm" @click="release()">
                     查询
                    </button>
                </div>
              <table class="table table-bordered">
                <thead>
                  <tr>
                    <th>用户名称</th>
                    <th>数量</th>
                    <th>充值时间</th>
                  </tr>
                </thead>
                <tbody>
                  <!-- <table-loading :loadding="loading" :colspan-num="10"></table-loading> -->
                  <tr v-for="item in data.data.data">
                      <td>{{item.name}}</td>
                      <td>{{item.add_number}}</td>
                      <td>{{item.created_time}}</td>
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
          userName:"",
          start_time:"",
          end_time:"",
          total:0
        }
      },
   mounted (){
     this.getAdSpaces();
   },
   methods: {
       release(){
            this.getAdSpaces()
       },
     onDetails (id){
        this.$router.push('/Details/' + id)
     },
     getAdSpaces () {
       request.post(
         createRequestURI('usdt/recharge'),
         {
            page:this.page,
            name:this.userName,
            beginTime:this.start_time,
            endTime:this.end_time
          },
         {validateStatus: status => status === 200})
           .then(response => {
           this.data = response.data;
           this.total = response.data.data.total;
           console.log(this.total)
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
