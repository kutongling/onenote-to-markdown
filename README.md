# OneNote转Markdown工具

![版本](https://img.shields.io/badge/版本-1.0-blue.svg)
![许可证](https://img.shields.io/badge/许可-MIT-green.svg)
![更新日期](https://img.shields.io/badge/更新-2025--03--06-brightgreen.svg)

一个简单高效的工具，用于将 OneNote 笔记（带有缩进层级和中文编号）转换为标准 Markdown 格式。特别适合处理课程笔记、考试大纲和学习资料整理。

## ✨ 功能特点

- **中文编号标题识别**：自动将"一、二、三、..."等中文编号转换为 Markdown 标题格式
- **多级缩进结构保留**：精确保留并转换 OneNote 中的缩进层次为 Markdown 列表
- **简洁直观的用户界面**：一键转换，即时预览结果
- **多种部署选项**：支持独立 HTML 网页、GitHub Pages 和 WordPress 内嵌
- **纯客户端处理**：所有转换在浏览器中完成，无需服务器支持
- **响应式设计**：适配桌面和移动设备

## 🚀 使用方法

### 在线使用

访问 [OneNote 转 Markdown 工具](https://kutongling.github.io/onenote-to-markdown/) 在线使用。

### 本地使用

1. 下载 `index.html` 文件到本地
2. 在任意浏览器中直接打开此文件
3. 粘贴 OneNote 内容，点击"转换为 Markdown"按钮

### WordPress 集成

1. 创建新页面或编辑现有页面
2. 切换到"文本"或"HTML"编辑模式（非可视化编辑器模式）
3. 复制下方的 WordPress 兼容代码
4. 粘贴代码到编辑器中
5. 发布/更新页面

```html
<div class="ontmd-tool" id="ontmd-tool-wp">
    <h2 style="text-align:center; margin-bottom:20px;">OneNote 笔记转 Markdown 工具</h2>
    
    <div style="margin-bottom:20px; padding:10px 15px; background:#f8f9fa; border-radius:5px; border:1px solid #e9ecef;">
        <label style="margin-right:15px;"><input type="checkbox" id="opt-headings" checked> 保留中文编号标题</label>
        <label style="margin-right:15px;"><input type="checkbox" id="opt-nesting" checked> 保留缩进层次</label>
        <label><input type="checkbox" id="opt-clean" checked> 清理空行</label>
    </div>
    
    <div style="display:flex; flex-wrap:wrap; gap:20px; margin-bottom:20px;">
        <div style="flex:1; min-width:250px;">
            <div style="display:flex; justify-content:space-between; margin-bottom:8px;">
                <strong>OneNote 原文</strong>
                <div>
                    <button id="example-btn" style="margin-right:5px; padding:4px 8px; background:none; border:1px solid #007bff; color:#007bff; border-radius:4px; cursor:pointer;">示例</button>
                    <button id="clear-btn" style="padding:4px 8px; background:none; border:1px solid #6c757d; color:#6c757d; border-radius:4px; cursor:pointer;">清空</button>
                </div>
            </div>
            <textarea id="input-area" style="width: 100%; height: 350px; padding: 12px; border: 1px solid rgb(206, 212, 218); border-radius: 4px; font-family: monospace; resize: vertical;" placeholder="粘贴您的OneNote文本到这里...
（保留原始缩进格式）"></textarea>
        </div>
        
        <div style="flex:1; min-width:250px;">
            <div style="display:flex; justify-content:space-between; margin-bottom:8px;">
                <strong>Markdown 结果</strong>
                <button id="copy-btn" style="padding:4px 8px; background:none; border:1px solid #28a745; color:#28a745; border-radius:4px; cursor:pointer;">复制</button>
            </div>
            <textarea id="output-area" style="width:100%; height:350px; padding:12px; border:1px solid #ced4da; border-radius:4px; font-family:monospace; resize:vertical; background:#f8f9fa;" readonly placeholder="转换后的Markdown将显示在这里..."></textarea>
        </div>
    </div>
    
    <div id="status-msg" style="display:none; margin:15px 0; padding:10px; border-radius:5px; text-align:center;"></div>
    
    <div style="text-align:center; margin:15px 0;">
        <button id="convert-btn" style="padding:10px 25px; background:#007bff; color:white; border:none; border-radius:4px; cursor:pointer; font-weight:500;">转换为 Markdown</button>
    </div>

    <div style="text-align:center; margin-top:20px; font-size:0.9em; color:#6c757d;">
        <p>© 2023-2025 <a href="https://github.com/kutongling" target="_blank">kutongling</a> - MIT 许可证</p>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // 延迟初始化以避免可能的加载问题
    setTimeout(initTool, 100);
    
    function initTool() {
        // 示例文本
        var exampleText = '一、"模糊"中探索：一条遥远的改编"渐近线"\n\t\t新中国成立初期，"文华""昆仑"两大私营电影公司，积极接身到讴歌新时代的创作中。\n\t\t\t1951年《武训传》批判运动\n\t\t\t1956年夏衍根据鲁迅小说改编的电影《祝福》\n二、主流叙事话语的建立：革命战争与革命历史题材的改编\n\t革命题材电影的特征\n\t\t1.注重对于"英雄人物"的塑造，"崭新的"人物形象手法\n\t\t\t（1）身份改写\n\t\t\t（2）挪移嫁接';
        
        // 获取元素
        var inputArea = document.getElementById('input-area');
        var outputArea = document.getElementById('output-area');
        var convertBtn = document.getElementById('convert-btn');
        var clearBtn = document.getElementById('clear-btn');
        var exampleBtn = document.getElementById('example-btn');
        var copyBtn = document.getElementById('copy-btn');
        var statusMsg = document.getElementById('status-msg');
        
        // 绑定事件
        convertBtn.onclick = function() {
            try {
                if (!inputArea.value.trim()) {
                    showStatus('请输入OneNote文本', 'error');
                    return;
                }
                
                var result = convertContent(inputArea.value);
                outputArea.value = result;
                showStatus('转换成功！', 'success');
            } catch (err) {
                showStatus('转换出错: ' + err.message, 'error');
            }
        };
        
        clearBtn.onclick = function() {
            inputArea.value = '';
            inputArea.focus();
        };
        
        exampleBtn.onclick = function() {
            inputArea.value = exampleText;
            showStatus('已加载示例', 'info');
        };
        
        copyBtn.onclick = function() {
            if (!outputArea.value) {
                showStatus('没有内容可复制', 'error');
                return;
            }
            
            outputArea.select();
            try {
                document.execCommand('copy');
                showStatus('已复制到剪贴板！', 'success');
            } catch (err) {
                showStatus('复制失败，请手动复制', 'error');
            }
        };
        
        // 辅助函数
        function showStatus(message, type) {
            statusMsg.textContent = message;
            statusMsg.style.display = 'block';
            
            if (type === 'success') {
                statusMsg.style.backgroundColor = '#d4edda';
                statusMsg.style.color = '#155724';
            } else if (type === 'error') {
                statusMsg.style.backgroundColor = '#f8d7da';
                statusMsg.style.color = '#721c24';
            } else {
                statusMsg.style.backgroundColor = '#d1ecf1';
                statusMsg.style.color = '#0c5460';
            }
            
            setTimeout(function() {
                statusMsg.style.display = 'none';
            }, 3000);
        }
        
        // 核心转换功能
        function convertContent(text) {
            var preserveHeadings = document.getElementById('opt-headings').checked;
            var preserveNesting = document.getElementById('opt-nesting').checked;
            var cleanText = document.getElementById('opt-clean').checked;
            
            // 分割行
            var lines = text.split('\n');
            
            // 清理空行
            if (cleanText) {
                var cleanedLines = [];
                for (var j = 0; j < lines.length; j++) {
                    if (lines[j].trim().length > 0) {
                        cleanedLines.push(lines[j]);
                    }
                }
                lines = cleanedLines;
            }
            
            var result = '';
            var lastIndentLevel = -1;
            
            // 处理每一行
            for (var i = 0; i < lines.length; i++) {
                var line = lines[i];
                var cleanedLine = line;
                
                // 计算缩进级别
                var indentLevel = 0;
                if (preserveNesting) {
                    // 使用Tab计算缩进
                    var tabMatch = line.match(/^(\t+)/);
                    if (tabMatch) {
                        indentLevel = tabMatch[1].length;
                        cleanedLine = line.replace(/^\t+/, '');
                    } else {
                        // 使用空格计算缩进
                        var spaceMatch = line.match(/^( +)/);
                        if (spaceMatch) {
                            indentLevel = Math.floor(spaceMatch[1].length / 4);
                            cleanedLine = line.replace(/^ +/, '');
                        }
                    }
                }
                
                // 检查是否是中文编号标题行
                var isChineseHeading = /^[一二三四五六七八九十]+、/.test(cleanedLine.trim());
                
                // 处理标题行 - 避免使用&&
                var isHeading = false;
                if (isChineseHeading) {
                    if (preserveHeadings) {
                        isHeading = true;
                    }
                }
                
                if (isHeading) {
                    // 添加空行，除非是第一行
                    if (i > 0) {
                        result += '\n';
                    }
                    
                    // 添加Markdown标题格式
                    result += '## ' + cleanedLine.trim() + '\n';
                    lastIndentLevel = -1; // 重置缩进级别
                } else {
                    // 处理普通内容行
                    if (preserveNesting) {
                        // 如果缩进级别变小，添加空行以保持清晰的结构
                        var needEmptyLine = false;
                        if (i > 0) {
                            if (indentLevel < lastIndentLevel) {
                                needEmptyLine = true;
                            }
                        }
                        
                        if (needEmptyLine) {
                            result += '\n';
                        }
                        
                        // 根据缩进级别添加Markdown列表符号
                        var indent = '';
                        for (var k = 0; k < indentLevel; k++) {
                            indent += '  ';
                        }
                        result += indent + '- ' + cleanedLine.trim() + '\n';
                        lastIndentLevel = indentLevel;
                    } else {
                        // 不保留缩进，所有行都视为同一级别
                        result += '- ' + cleanedLine.trim() + '\n';
                    }
                }
            }
            
            return result.trim();
        }
    }
});
</script>
