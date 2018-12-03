<template>
  <div class="container-fluid" style="margin-top:10px;">

  <div class="panel panel-default">
    <div class="panel-heading">
        <div class="form-inline">
            <div class="form-group">
                <router-link to='review' class="btn btn-default" tag="button">
                审核
                </router-link>
            </div>
            <div class="form-group">
                <a class="btn btn-default" tag="button">
                配置审核
                </a>
            </div>
        </div>
    </div>
    <div class="panel-body">
      <div style="text-align: center">
        <el-transfer
          style="text-align: left; display: inline-block"
          v-model="value4"
          filterable
          :titles="['不需要审核', '需要审核']"
          :button-texts="['取消审核', '添加审核']"
          :format="{
            noChecked: '${total}',
            hasChecked: '${checked}/${total}'
          }"
          @change="handleChange"
          :data="data">
          <span slot-scope="{ option }">{{ option.label }}</span>
        </el-transfer>
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
.term{
    margin-left: 15px;
}
.transfer-footer {
  margin-left: 20px;
  padding: 6px 5px;
}
</style>


<script>
import lodash from "lodash";
import { mapGetters } from "vuex";
import request, { createRequestURI } from "../../util/request";

const AccountTermReview = {
  /**
   * 定义当前组件状态数据
   *
   * @return {Object}
   * @author Seven Du <shiweidu@outlook.com>
   * @homepage http://medz.cn
   */
    data() {
      return {
        data: [],
        value4: [],
      };
    },
    created(){
      this.getAdSpaces()
    },
    methods: {
      handleChange(value, direction, movedKeys) {
        if(direction == "right"){
            for(let i = 0;i<value.length;i++){
                this.data[value[i]].status = 0
            }
        } else{
            for(let i = 0;i<movedKeys.length;i++){
                this.data[movedKeys[i]].status = 1
            }
        }
        this.postAdSpaces()
      },
      getAdSpaces () {
             request.get(
               createRequestURI('get/tokens'),
               { validateStatus: status => status === 200 }
             ).then(response => {
                  let data = [];
                 for (let i = 0; i <= response.data.length-1; i++) {
                    if(response.data[i].status == 0){
                        this.value4.push(i)
                    }
                   data.push({
                     key: i,
                     label: response.data[i].token_name ,
                     id:response.data[i].id,
                     token_name:response.data[i].token_name,
                     status: response.data[i].status
                   });
                 }
                 this.data = data
             })
         },
         postAdSpaces () {
              request.post(
                createRequestURI('poundage/setting'),
                {TokenModel:this.data},
                { validateStatus: status => status === 200 }
              ).then(response => {
                  this.getAdSpaces()
              })
          },
    }
};
export default AccountTermReview;
</script>

