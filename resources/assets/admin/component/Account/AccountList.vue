<template>
        <div class="container-fluid" style="margin-top:10px;">
            <div class="panel-body">
              <table class="table table-bordered">
                <thead>
                  <tr>
                    <th>权证名称</th>
                    <th>状态</th>
                    <th>发布日期</th>
                    <th>到期日期</th>
                    <th>操作</th>
                  </tr>
                </thead>
                <tbody>
                  <!-- <table-loading :loadding="loading" :colspan-num="10"></table-loading> -->
                  <tr v-for="item in data">
                    <td>{{item.title}}</td>
                    <td v-if="item.withdraw == 1">已下架</td>
                    <td v-if="item.withdraw != 1">
                        <span v-if="item.end_status == 1">进行中</span>
                        <span v-if="item.end_status == 2">已完成</span>
                        <span v-if="item.end_status == 3">未开始</span>
                    </td>
                    <td>{{item.create_time*1000 | time}}</td>
                    <td>{{item.pay_end_time*1000 | time}}</td>
                    <td class='action'>
                        <a @click="revoke(item.id)" v-if="item.withdraw != 1">下架</a>
                        <a @click="$router.push('/View/' + item.id)">查看</a>
                    </td>
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
.action{
  display: flex;
  justify-content:space-around;
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
     this.requestLogs();
   },
   methods: {
       release(){
            this.requestLogs()
       },
     onDetails (id){
        this.$router.push('/Details/' + id)
     },
     requestLogs () {
         request.get(
            createRequestURI('product/lists'),
            {params:{page:this.page},
             validateStatus: status => status === 200 }
          ).then(response => {
              this.data = response.data.data
              this.total = parseInt(response.data.total)
          })
     },
     handleCurrentChange(val){
        this.page = val
        this.requestLogs()
     },
     revoke(id){
     request.post(
                 createRequestURI('product/withdraw'),
                 {id:id},
                  {validateStatus: status => status === 200 }
               ).then(response => {
               if(response.data.code == 200){
                    alert('下架成功')
               } else {
                    alert('下架失败')
               }
               this.requestLogs()
               console.log(response)
               })

     }
 }
};
export default AccountHome;
</script>
