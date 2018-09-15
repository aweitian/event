## 安装
> composer require aweitian/event

## 代码基本借鉴symfony

## 事件的几个基本概念
- 事件，事件有一个是否停止冒泡属性，事件在派遣过程中按优先级从高到低调用LISTENER，如果事件【停止冒泡】为真，停止调用下一个LISTENER
- LISTENER，可被call_user_func函数调用即可的CALLBACK。形参：$event, $eventName, $DISPATCHER。
- 订阅者，可以理解成一个人，他有一个清单，清单上有TA感兴趣的事件列表，所以添加订阅者就是根据清单添加LISTENER
- DISPATCHER，相当于一个机构，TA负责EMIT/DISPATCH事件，【订阅者】也找TA订阅事件，当事件到来时，它负责通知订阅者（调用订阅者那清单）

## 订阅者清单格式
- 事件名 => 字符串 这个字符串作为订阅者的一个方法
- 事件名 => [字符串,priority] 这个字符串作为订阅者的一个方法
- 事件名 => [[字符串],[字符串,priority],...] 这个字符串作为订阅者的一个方法

## LISTENER的返回值没有意义

## addListener
> 函数返回了LISTENER，可以用于removeListener,这个和symfony不同