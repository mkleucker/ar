package edu.dhbw.andobjviewer.graphics;

import java.nio.FloatBuffer;
import java.util.ArrayList;

import javax.microedition.khronos.opengles.GL10;

import android.util.Log;
import edu.dhbw.andar.interfaces.OpenGLRenderer;
import edu.dhbw.andar.util.GraphicsUtil;
import edu.dhbw.andobjviewer.models.Model;

public class LightingRenderer implements OpenGLRenderer {

	private ArrayList<Model3D> models;

	private float[] ambientlight0 = {.3f, .3f, .3f, 1f};
	private float[] diffuselight0 = {.7f, .7f, .7f, 1f};
	private float[] specularlight0 = {0.6f, 0.6f, 0.6f, 1f};
	private float[] lightposition0 = {100.0f, -200.0f, 200.0f, 0.0f};

	private FloatBuffer lightPositionBuffer0 = GraphicsUtil.makeFloatBuffer(lightposition0);
	private FloatBuffer specularLightBuffer0 = GraphicsUtil.makeFloatBuffer(specularlight0);
	private FloatBuffer diffuseLightBuffer0 = GraphicsUtil.makeFloatBuffer(diffuselight0);
	private FloatBuffer ambientLightBuffer0 = GraphicsUtil.makeFloatBuffer(ambientlight0);


	private float[] ambientlight1 = {.3f, .3f, .3f, 1f};
	private float[] diffuselight1 = {.7f, .7f, .7f, 1f};
	private float[] specularlight1 = {0.6f, 0.6f, 0.6f, 1f};
	private float[] lightposition1 = {20.0f, -40.0f, 100.0f, 1f};

	private FloatBuffer lightPositionBuffer1 = GraphicsUtil.makeFloatBuffer(lightposition1);
	private FloatBuffer specularLightBuffer1 = GraphicsUtil.makeFloatBuffer(specularlight1);
	private FloatBuffer diffuseLightBuffer1 = GraphicsUtil.makeFloatBuffer(diffuselight1);
	private FloatBuffer ambientLightBuffer1 = GraphicsUtil.makeFloatBuffer(ambientlight1);

	private float[] ambientlight2 = {.4f, .4f, .4f, 1f};
	private float[] diffuselight2 = {.7f, .7f, .7f, 1f};
	private float[] specularlight2 = {0.6f, 0.6f, 0.6f, 1f};
	private float[] lightposition2 = {5f, -3f, -20f, 1.0f};

	private FloatBuffer lightPositionBuffer2 = GraphicsUtil.makeFloatBuffer(lightposition2);
	private FloatBuffer specularLightBuffer2 = GraphicsUtil.makeFloatBuffer(specularlight2);
	private FloatBuffer diffuseLightBuffer2 = GraphicsUtil.makeFloatBuffer(diffuselight2);
	private FloatBuffer ambientLightBuffer2 = GraphicsUtil.makeFloatBuffer(ambientlight2);

	private float[] ambientlight3 = {.4f, .4f, .4f, 1f};
	private float[] diffuselight3 = {.4f, .4f, .4f, 1f};
	private float[] specularlight3 = {0.6f, 0.6f, 0.6f, 1f};
	private float[] lightposition3 = {0, 0f, -1f, 0.0f};

	private FloatBuffer lightPositionBuffer3 = GraphicsUtil.makeFloatBuffer(lightposition3);
	private FloatBuffer specularLightBuffer3 = GraphicsUtil.makeFloatBuffer(specularlight3);
	private FloatBuffer diffuseLightBuffer3 = GraphicsUtil.makeFloatBuffer(diffuselight3);
	private FloatBuffer ambientLightBuffer3 = GraphicsUtil.makeFloatBuffer(ambientlight3);


	public void setModels(ArrayList<Model3D> models) {
		this.models = models;
	}

	/**
	 * Painting loop. Use this to read changing values & interact with the models.
	 *
	 * @param gl OpenGL Context (ignore)
	 */
	public final void draw(GL10 gl) {

		if (this.models != null && this.models.size() > 1) {

			boolean [] visible = new boolean[this.models.size()];
			int visibleCount = 0;
			double distAvg = 0.0;
			for (int i = 0; i < this.models.size(); i++) {
				Model3D m3d = this.models.get(i);
				Model m = m3d.getModel();
				m.yrot += 1.0f;
				m.zrot += 0.4f;
				if(m3d.isVisible()) {
					visible[i] = true;
					visibleCount++;
				} else {
					visible[i] = false;
				}
			}

			if (visibleCount == 1) {
				int index = 0;
				for (int i = 0; i < this.models.size(); i++) {
					if (visible[i] == true) {
						index = i;
					}
				}

				Model3D m3d = this.models.get(index);
				Model m = m3d.getModel();
				double[] mat = m3d.getTransMatrix();
				double d = Math.sqrt(mat[3] * mat[3] + mat[7] * mat[7] + mat[11] * mat[11]);

				m.setFixedScale((float)d / 50.0f);

			} else if(visibleCount > 1) {
				for (int i = 0; i < models.size(); i++) {
					for (int j = i + 1; j < models.size(); j++) {
						Model3D m3d1 = this.models.get(i);
						Model3D m3d2 = this.models.get(j);
						Model m1 = m3d1.getModel();
						Model m2 = m3d2.getModel();
						double[] mat1 = m3d1.getTransMatrix();
						double[] mat2 = m3d2.getTransMatrix();
						if (visible[i] && visible[j]) {
							distAvg += 2 * Math.sqrt(Math.pow(mat1[3] - mat2[3], 2) + Math.pow(mat1[7] - mat2[7], 2)) / ((double) visibleCount * (double)(visibleCount - 1));
						}
					}
				}
				for (int i = 0; i < models.size(); i++) {
					if (visible[i]) {
						this.models.get(i).getModel().setFixedScale((float)distAvg / 20.0f);
					}
				}
			}
			/*
			   if (m0.isVisible() && m1.isVisible()) {
			   double[] o1TransMatrix = m0.getTransMatrix();
			   double[] o2TransMatrix = m1.getTransMatrix();

			   double distance = Math.sqrt(Math.pow(o1TransMatrix[3] - o2TransMatrix[3], 2) + Math.pow(o1TransMatrix[7] - o2TransMatrix[7], 2));
			   Log.d(LightingRenderer.class.getSimpleName(), String.format("Distance: %.2f.", distance));
			   Log.d(LightingRenderer.class.getSimpleName(), String.format("Matrix: %d.", o1TransMatrix.length));


			   mm0.setFixedScale((float)d / 20.0f);
			   mm1.setFixedScale((float)distance / 20.0f);
			//mm0.setFixedScale(5);
			//mm1.setFixedScale(20);

			   }
			   */
		}

	}


	public final void setupEnv(GL10 gl) {
		gl.glLightfv(GL10.GL_LIGHT0, GL10.GL_AMBIENT, ambientLightBuffer0);
		gl.glLightfv(GL10.GL_LIGHT0, GL10.GL_DIFFUSE, diffuseLightBuffer0);
		gl.glLightfv(GL10.GL_LIGHT0, GL10.GL_SPECULAR, specularLightBuffer0);
		gl.glLightfv(GL10.GL_LIGHT0, GL10.GL_POSITION, lightPositionBuffer0);
		gl.glEnable(GL10.GL_LIGHT0);
		gl.glLightfv(GL10.GL_LIGHT1, GL10.GL_AMBIENT, ambientLightBuffer1);
		gl.glLightfv(GL10.GL_LIGHT1, GL10.GL_DIFFUSE, diffuseLightBuffer1);
		gl.glLightfv(GL10.GL_LIGHT1, GL10.GL_SPECULAR, specularLightBuffer1);
		gl.glLightfv(GL10.GL_LIGHT1, GL10.GL_POSITION, lightPositionBuffer1);
		gl.glEnable(GL10.GL_LIGHT1);
		gl.glLightfv(GL10.GL_LIGHT2, GL10.GL_AMBIENT, ambientLightBuffer2);
		gl.glLightfv(GL10.GL_LIGHT2, GL10.GL_DIFFUSE, diffuseLightBuffer2);
		gl.glLightfv(GL10.GL_LIGHT2, GL10.GL_SPECULAR, specularLightBuffer2);
		gl.glLightfv(GL10.GL_LIGHT2, GL10.GL_POSITION, lightPositionBuffer2);
		gl.glEnable(GL10.GL_LIGHT2);
		gl.glLightfv(GL10.GL_LIGHT3, GL10.GL_AMBIENT, ambientLightBuffer3);
		gl.glLightfv(GL10.GL_LIGHT3, GL10.GL_DIFFUSE, diffuseLightBuffer3);
		gl.glLightfv(GL10.GL_LIGHT3, GL10.GL_SPECULAR, specularLightBuffer3);
		gl.glLightfv(GL10.GL_LIGHT3, GL10.GL_POSITION, lightPositionBuffer3);
		gl.glEnable(GL10.GL_LIGHT3);
		initGL(gl);
	}

	@Override
	public final void initGL(GL10 gl) {
		gl.glDisable(GL10.GL_COLOR_MATERIAL);
		gl.glShadeModel(GL10.GL_SMOOTH);
		gl.glEnable(GL10.GL_LIGHTING);
		//gl.glEnable(GL10.GL_CULL_FACE);
		gl.glEnable(GL10.GL_DEPTH_TEST);
		gl.glEnable(GL10.GL_NORMALIZE);
		gl.glEnable(GL10.GL_RESCALE_NORMAL);
	}

}
