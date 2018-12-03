<template>
        <div class="container-fluid" style="margin-top:10px;">
          <div class="panel panel-default">
            <div class="panel-heading">
            搜索：<input type="text" v-model="userName" placeholder="id ">
            <input type="text" v-model="userId" placeholder="用户名称 ">
            <button class="btn btn-primary btn-sm" @click="release()">
                            搜索
            </button>
            </div>
            <div class="panel-body">
              <table class="table table-bordered">
                <thead>
                  <tr>
                    <th>#ID</th>
                    <th>用户名称</th>
                    <th>账户金额</th>
                    <th>明细记录</th>
                  </tr>
                </thead>
                <tbody>
                  <!-- <table-loading :loadding="loading" :colspan-num="10"></table-loading> -->
                  <tr v-for="item in data.data">
                    <td>{{item.id}}</td>
                    <td>{{item.name}}</td>
                    <td>
                        <span v-if="!item.wallet.length">0</span>
                        <span v-for="wallet in item.wallet" v-if="item.wallet">
                        {{wallet.balance}}{{wallet.type_name}},
                        </span>
                    </td>
                    <td><a @click="onDetails(item.id)">详情</a></td>
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

const AccountHome = {
  /**
   * 定义当前组件状态数据
   *
   * @return {Object}
   * @author Seven Du <shiweidu@outlook.com>
   * @homepage http://medz.cn
   */
   data: () => ({
      data:{},
      userName:"",
      userId:"",
      total:0,
      page:0
   }),
   mounted (){
     this.getAdSpaces();
   },
   methods: {
       release(){
            this.getAdSpaces();
       },
     onDetails (id){
        this.$router.push('/Details/' + id);
     },
     getAdSpaces () {
       request.post(
         createRequestURI('account'),
         {
            page:this.page,
            userId:this.userName,
            name:this.userId
         },
         {validateStatus: status => status === 200})
           .then(response => {
           console.log(response.data.total)
         this.data = response.data;
         this.total = response.data.total;
       }).catch(({ response: { data: { errors = ['加载认证类型失败'] } = {} } = {} }) => {
       });
     },
      handleCurrentChange(val){
        this.page = val;
        this.getAdSpaces();
      }
 }
};
export default AccountHome;
</script>
